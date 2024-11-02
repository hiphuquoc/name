<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BlogRequest;
use App\Helpers\Upload;
use App\Models\CategoryBlog;
use App\Models\Blog;
use App\Models\Seo;
use App\Models\RelationCategoryBlogBlogInfo;
use App\Models\RelationSeoBlogInfo;
use App\Models\Prompt;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class BlogController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo tên */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewBlogInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        $categories         = CategoryBlog::all();
        $list               = Blog::getList($params);
        return view('admin.blog.list', compact('list', 'categories', 'params', 'viewPerPage'));
    }

    public function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* kiểm tra xem ngôn ngữ có nằm trong danh sách không */
        $flagView           = false;
        foreach(config('language') as $ld){
            if($ld['key']==$language) {
                $flagView   = true;
                break;
            }
        }
        /* tìm theo ngôn ngữ */
        $item               = Blog::select('*')
                                ->where('id', $id)
                                ->with('seo.contents', 'seos.infoSeo.contents', 'seos.infoSeo.jobAutoTranslate')
                                ->first();
        if(empty($item)) $flagView = false;
        if($flagView==true){
            /* chức năng copy source */
            $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
            $itemSourceToCopy   = Blog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($idSeoSourceToCopy){
                                        $query->where('id', $idSeoSourceToCopy);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
            $itemSeoSourceToCopy    = [];
            if(!empty($itemSourceToCopy->seos)){
                foreach($itemSourceToCopy->seos as $s){
                    if($s->infoSeo->language==$language) {
                        $itemSeoSourceToCopy = $s->infoSeo;
                        break;
                    }
                }
            }
            /* lấy item seo theo ngôn ngữ được chọn */
            $itemSeo            = [];
            if(!empty($item->seos)){
                foreach($item->seos as $s){
                    if($s->infoSeo->language==$language) {
                        $itemSeo = $s->infoSeo;
                        break;
                    }
                }
            }
            /* prompts */
            $prompts            = Prompt::select('*')
                                    ->where('reference_table', 'category_blog')
                                    ->get();
            /* trang canonical -> cùng là sản phẩm */
            $idProduct          = $item->id ?? 0;
            $sources            = Blog::select('*')
                                    ->whereHas('seos.infoSeo', function($query) use($language){
                                        $query->where('language', $language);
                                    })
                                    ->where('id', '!=', $idProduct)
                                    ->get();
            /* type */
            $type               = !empty($itemSeo) ? 'edit' : 'create';
            $type               = $request->get('type') ?? $type;
            /* trang cha */
            $parents            = CategoryBlog::all();
            /* category cha */
            return view('admin.blog.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'message'));
        } else {
            return redirect()->route('admin.blog.list');
        }
    }

    public function createAndUpdate(Request $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id');
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idBlog             = $request->get('blog_info_id');
            $language           = $request->get('language');
            $categoryType       = 'blog_info';
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $categoryType, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
            }
            /* kiểm tra insert thành công không */
            if(!empty($idSeo)){
                /* insert seo_content */
                if(!empty($request->get('content'))) CategoryController::insertAndUpdateContents($idSeo, $request->get('content'));
                if($language=='vi'){
                    /* insert hoặc update blog_info */
                    $status           = !empty($request->get('status'))&&$request->get('status')=='on' ? 1 : 0;
                    $outstanding      = !empty($request->get('outstanding'))&&$request->get('outstanding')=='on' ? 1 : 0;
                    if(empty($idBlog)){ /* check xem create category hay update category */
                        $idBlog          = Blog::insertItem([
                            'status'        => $status,
                            'outstanding'   => $outstanding,
                            'seo_id'        => $idSeo,
                        ]);
                    }else {
                        Blog::updateItem($idBlog, [
                            'status'        => $status,
                            'outstanding'   => $outstanding,
                        ]);
                    }
                    /* insert relation_category_blog_blog_info */
                    RelationCategoryBlogBlogInfo::select('*')
                        ->where('blog_info_id', $idBlog)
                        ->delete();
                    if(!empty($request->get('categories'))){
                        foreach($request->get('categories') as $idCategoryBlog){
                            RelationCategoryBlogBlogInfo::insertItem([
                                'category_blog_id'  => $idCategoryBlog,
                                'blog_info_id'      => $idBlog
                            ]);
                        }
                    }
                }
                /* relation_seo_blog_info */
                $relationSeoBlogInfo = RelationSeoBlogInfo::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->where('blog_info_id', $idBlog)
                                        ->first();
                if(empty($relationSeoBlogInfo)) RelationSeoBlogInfo::insertItem([
                    'seo_id'        => $idSeo,
                    'blog_info_id'   => $idBlog
                ]);
                DB::commit();
                /* Message */
                $message        = [
                    'type'      => 'success',
                    'message'   => '<strong>Thành công!</strong> Đã cập nhật Bài Viết!'
                ];
                /* nếu có tùy chọn index => gửi google index */
                if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                    $flagIndex = IndexController::indexUrl($idSeo);
                    if($flagIndex==200){
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Bài Viết và Báo Google Index!';
                    }else {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Bài Viết <span style="color:red;">nhưng báo Google Index lỗi</span>';
                    }
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
        }
        /* có lỗi mặc định Message */
        if(empty($message)){
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.blog.view', ['id' => $idBlog, 'language' => $language]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Blog::select('*')
                                ->where('id', $id)
                                ->with('seo', 'seos')
                                ->first();
                /* xóa ảnh đại diện trên google_clouds */ 
                Upload::deleteWallpaper($info->seo->image);
                /* delete relation */
                $info->categories()->delete();
                /* delete các trang seos ngôn ngữ */
                foreach($info->seos as $s){
                    /* xóa ảnh đại diện trên google_clouds */ 
                    Upload::deleteWallpaper($s->infoSeo->image);
                    foreach($s->infoSeo->contents as $c) $c->delete();
                    $s->infoSeo()->delete();
                    $s->delete();
                }
                $info->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }
}
