<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\CategoryRequest;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Prompt;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\RelationCategoryInfoCategoryBlogInfo;
use App\Models\RelationSeoCategoryInfo;

class CategoryController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params     = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = Category::getTreeCategory(['seo.language' => 'vi']);
        return view('admin.category.list', compact('list', 'params'));
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* chức năng copy source */
        $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
        $itemSourceToCopy   = Category::select('*')
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
        /* tìm theo ngôn ngữ */
        $item               = Category::select('*')
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
        $arrayTypeCategory = [];
        foreach(config('main.category_type') as $c) $arrayTypeCategory[] = $c['key'];
        $prompts            = Prompt::select('*')
                                ->whereIn('reference_table', $arrayTypeCategory)
                                ->get();
        $parents            = Category::all();
        /* category blog */
        $categoryBlogs      = CategoryBlog::all();
        /* trang canonical -> cùng là sản phẩm */
        $idProduct          = $item->id ?? 0;
        $sources            = Category::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', '!=', $idProduct)
                                ->get();
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.category.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'categoryBlogs', 'message'));
    }

    public function createAndUpdate(CategoryRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id');
            $idCategory         = $request->get('category_info_id');
            $language           = $request->get('language');
            $categoryType       = $request->get('category_type') ?? null;
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $categoryType, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo);
            }
            
            if($language=='vi'){
                /* insert hoặc update category_info */
                $flagShow           = !empty($request->get('flag_show'))&&$request->get('flag_show')=='on' ? 1 : 0;
                if(empty($idCategory)){ /* check xem create category hay update category */
                    $idCategory          = Category::insertItem([
                        'flag_show'     => $flagShow,
                        'seo_id'        => $idSeo,
                    ]);
                }else {
                    Category::updateItem($idCategory, [
                        'flag_show'     => $flagShow,
                    ]);
                }
                /* insert relation_category_info_category_blog_id */
                RelationCategoryInfoCategoryBlogInfo::select('*')
                    ->where('category_info_id', $idCategory)
                    ->delete();
                if(!empty($request->get('category_blog_info_id'))){
                    foreach($request->get('category_blog_info_id') as $idCategoryBlogInfo){
                        RelationCategoryInfoCategoryBlogInfo::insertItem([
                            'category_info_id'      => $idCategory,
                            'category_blog_info_id' => $idCategoryBlogInfo
                        ]);
                    }
                }
            }
            
            /* relation_seo_category_info */
            $relationSeoCategoryInfo = RelationSeoCategoryInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('category_info_id', $idCategory)
                                    ->first();
            if(empty($relationSeoCategoryInfo)) RelationSeoCategoryInfo::insertItem([
                'seo_id'        => $idSeo,
                'category_info_id'   => $idCategory
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
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Category!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if($request->get('index_google')==true) {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Category và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Category <span style="color:red;">nhưng báo Google Index lỗi</span>';
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
        return redirect()->route('admin.category.view', ['id' => $idCategory, 'language' => $language]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Category::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo', 'products', 'blogs', 'freeWallpapers')
                                ->first();
                /* xóa ảnh đại diện trong thư mục */ 
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* delete relation */
                $info->products()->delete();
                $info->blogs()->delete();
                $info->freeWallpapers()->delete();
                $info->files()->delete();
                /* delete các trang seos ngôn ngữ */
                foreach($info->seos as $s){
                    $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($s->infoSeo->image_small));
                    if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                    $imagePath          = Storage::path(config('admin.images.folderUpload').basename($s->infoSeo->image));
                    if(file_exists($imagePath)) @unlink($imagePath);
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
