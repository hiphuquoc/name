<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\TagRequest;
use App\Models\Seo;
use App\Models\LanguageTagInfo;
use App\Models\Prompt;
use App\Models\Tag;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\RelationTagInfoCategoryBlogInfo;
use App\Models\RelationEnCategoryInfoEnCategoryBlogInfo;
use App\Models\RelationCategoryInfoTagInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\SeoContent;

class TagController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* Search theo danh mục */
        if(!empty($request->get('search_category'))) $params['search_category'] = $request->get('search_category');
        /* paginate */
        $viewPerPage        = Cookie::get('viewTagInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Tag::getList($params);
        $categories         = Category::select('*')
                                ->get();
        return view('admin.tag.list', compact('list', 'categories', 'params', 'viewPerPage'));
    }

    public static function view(Request $request){
        $keyTable           = 'tag_info';
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* chức năng copy source */
        $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
        $itemSourceToCopy   = Tag::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($idSeoSourceToCopy){
                                    $query->where('id', $idSeoSourceToCopy);
                                })
                                ->with(['files' => function($query) use($keyTable){
                                    $query->where('relation_table', $keyTable);
                                }])
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
        /* tìm theo ngôn ngữ */
        $item               = Tag::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo', 'seos')
                                ->first();
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
                                ->where('reference_table', $keyTable)
                                ->get();
        $parents            = Category::all();
        /* category blog */
        $categoryBlogs      = CategoryBlog::all();
        /* trang canonical -> cùng là sản phẩm */
        $idProduct          = $item->id ?? 0;
        $sources            = Tag::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', '!=', $idProduct)
                                ->get();
        $tmp                = Category::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', '!=', $idProduct)
                                ->get();
        $sources            = $sources->concat($sources)->concat($tmp);
        /* categories cha */
        $categories         = Category::all();
        /* type */
        $type               = !empty($itemSeo) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.tag.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'categoryBlogs', 'categories', 'message'));
    }

    public function createAndUpdate(TagRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $keyTable           = 'tag_info';
            $idSeo              = $request->get('seo_id');
            $idTag              = $request->get('tag_info_id');
            $language           = $request->get('language');
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo);
            }
            
            if($language=='vi'){
                /* insert hoặc update tag_info */
                $flagShow           = !empty($request->get('flag_show'))&&$request->get('flag_show')=='on' ? 1 : 0;
                if(empty($idTag)){ /* check xem create tag hay update tag */
                    $idTag          = Tag::insertItem([
                        'flag_show' => $flagShow,
                        'seo_id'    => $idSeo
                    ]);
                }else {
                    Tag::updateItem($idTag, [
                        'flag_show' => $flagShow
                    ]);
                }
                /* insert relation_tag_info_category_blog_id */
                RelationTagInfoCategoryBlogInfo::select('*')
                    ->where('tag_info_id', $idTag)
                    ->delete();
                if(!empty($request->get('category_blog_info_id'))){
                    foreach($request->get('category_blog_info_id') as $idTagBlogInfo){
                        RelationTagInfoCategoryBlogInfo::insertItem([
                            'tag_info_id'      => $idTag,
                            'category_blog_info_id' => $idTagBlogInfo
                        ]);
                    }
                }
                /* insert relation_category_info_tag_info */
                RelationCategoryInfoTagInfo::select('*')
                    ->where('tag_info_id', $idTag)
                    ->delete();
                if(!empty($request->get('categories'))){
                    foreach($request->get('categories') as $idCategoryInfo){
                        RelationCategoryInfoTagInfo::insertItem([
                            'category_info_id'  => $idCategoryInfo,
                            'tag_info_id'       => $idTag
                        ]);
                    }
                }
            }
            /* relation_seo_tag_info */
            $relationSeoTagInfo = RelationSeoTagInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('tag_info_id', $idTag)
                                    ->first();
            if(empty($relationSeoTagInfo)) RelationSeoTagInfo::insertItem([
                'seo_id'        => $idSeo,
                'tag_info_id'   => $idTag
            ]);
            /* insert seo_content */
            SeoContent::select('*')
                ->where('seo_id', $idSeo)
                ->delete();
            foreach($request->get('content') as $content){
                SeoContent::insertItem([
                    'seo_id'    => $idSeo,
                    'content'   => $content
                ]);
            }
            
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Tag!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if($request->get('index_google')==true) {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Tag và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Tag! <span style="color:red;">nhưng báo Google Index lỗi</span>';
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.tag.view', ['id' => $idTag, 'language' => $language]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Tag::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo', 'products', 'blogs', 'freeWallpapers')
                                ->first();
                /* xóa ảnh đại diện trên google_clouds */ 
                Upload::deleteWallpaper($info->seo->image);
                /* delete relation */
                $info->products()->delete();
                $info->blogs()->delete();
                $info->freeWallpapers()->delete();
                $info->files()->delete();
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
