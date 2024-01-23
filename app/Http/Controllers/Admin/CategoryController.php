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
use App\Models\EnSeo;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\RelationCategoryInfoCategoryBlogInfo;
use App\Models\RelationEnCategoryInfoEnCategoryBlogInfo;
use App\Models\RelationSeoEnSeo;

class CategoryController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = Category::getTreeCategory();
        return view('admin.category.list', compact('list', 'params'));
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = Cookie::get('language') ?? 'vi';
        $item               = Category::select('category_info.*', 'seo.type')
                                ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                ->where('category_info.id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo', 'en_seo')
                                ->first();
        $idNot              = $item->id ?? 0;
        $parents            = Category::select('*')
                                ->where('id', '!=', $idNot)
                                ->get();
        /* category blog */
        $categoryBlogs      = CategoryBlog::all();
        /* content */
        $content            = null;
        if(!empty($item->seo->slug)){
            $content        = Storage::get(config('main.storage.contentCategory').$item->seo->slug.'.blade.php');
        }
        /* en content */
        $enContent          = null;
        if(!empty($item->en_seo->slug)){
            $enContent      = Storage::get(config('main.storage.enContentCategory').$item->en_seo->slug.'.blade.php');
        }
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.category.view', compact('item', 'type', 'parents', 'categoryBlogs', 'message', 'content', 'enContent'));
    }

    public function create(CategoryRequest $request){
        try {
            DB::beginTransaction();
            $language           = Cookie::get('language') ?? 'vi';
            $keyTable           = $request->get('type');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* insert page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            $seoId              = Seo::insertItem($insertSeo);
            $insertEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($request->all(), $keyTable, $dataPath);
            $enSeoId            = EnSeo::insertItem($insertEnSeo);
            /* kết nối bảng vi và en */
            RelationSeoEnSeo::insertItem([
                'seo_id'    => $seoId,
                'en_seo_id' => $enSeoId
            ]);
            /* upload icon */
            $iconPath           = null;
            if($request->hasFile('icon')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug').'-icon' : time();
                $iconPath       = Upload::uploadCustom($request->file('icon'), $name);
            }
            /* insert category_info */
            $flagShow           = !empty($request->get('flag_show'))&&$request->get('flag_show')=='on' ? 1 : 0;
            $idCategory         = Category::insertItem([
                'seo_id'        => $seoId,
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'icon'          => $iconPath,
                'flag_show'     => $flagShow,
                'en_seo_id'     => $enSeoId,
                'en_name'       => $request->get('en_name'),
                'en_description'=> $request->get('en_description')
            ]);
            /* insert relation_category_info_category_blog_id */
            if(!empty($request->get('category_blog_info_id'))){
                foreach($request->get('category_blog_info_id') as $idCategoryBlogInfo){
                    RelationCategoryInfoCategoryBlogInfo::insertItem([
                        'category_info_id'      => $idCategory,
                        'category_blog_info_id' => $idCategoryBlogInfo
                    ]);
                }
            }
            /* lưu content vào file */
            $content            = $request->get('content') ?? null;
            $content            = ImageController::replaceImageInContentWithLoading($content);
            if(!empty($content)) Storage::put(config('main.storage.contentCategory').$request->get('slug').'.blade.php', $content);
            $enContent          = $request->get('en_content') ?? null;
            $enContent          = ImageController::replaceImageInContentWithLoading($enContent);
            if(!empty($enContent)) Storage::put(config('main.storage.enContentCategory').$request->get('en_slug').'.blade.php', $enContent);
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idCategory)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idCategory,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* insert gallery và lưu CSDL */
            if($request->hasFile('gallery')&&!empty($idCategory)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idCategory,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                GalleryController::upload($request->file('gallery'), $params);
            }
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Dã tạo Category mới'
            ];
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.category.view', ['id' => $idCategory]);
    }

    public function update(CategoryRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $language           = Cookie::get('language') ?? 'vi';
            $keyTable           = $request->get('type');

            $seoId              = $request->get('seo_id');
            $enSeoId            = $request->get('en_seo_id');
            $idCategory         = $request->get('category_info_id');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $updateSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            Seo::updateItem($seoId, $updateSeo);
            if(!empty($enSeoId)){
                $updateEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($request->all(), $keyTable, $dataPath);
                EnSeo::updateItem($enSeoId, $updateEnSeo);
            }else {
                $insertEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($request->all(), $keyTable, $dataPath);
                $enSeoId            = EnSeo::insertItem($insertEnSeo);
            }
            /* kết nối bảng vi và en */
            RelationSeoEnSeo::select('*')
                            ->where('seo_id', $seoId)
                            ->delete();
            RelationSeoEnSeo::insertItem([
                'seo_id'    => $seoId,
                'en_seo_id' => $enSeoId
            ]);
            /* upload icon */
            $iconPath           = null;
            if($request->hasFile('icon')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug').'-icon' : time();
                $iconPath       = Upload::uploadCustom($request->file('icon'), $name);
            }
            /* insert category_info */
            $flagShow           = !empty($request->get('flag_show'))&&$request->get('flag_show')=='on' ? 1 : 0;
            $arrayUpdate        = [
                'flag_show'     => $flagShow,
                'seo_id'        => $seoId,
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'en_seo_id'     => $enSeoId,
                'en_name'       => $request->get('en_name'),
                'en_description'=> $request->get('en_description')
            ];
            if(!empty($iconPath)) $arrayUpdate['icon'] = $iconPath;
            Category::updateItem($idCategory, $arrayUpdate);
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
            /* lưu content vào file */
            $content            = $request->get('content') ?? null;
            $content            = ImageController::replaceImageInContentWithLoading($content);
            if(!empty($content)) {
                Storage::put(config('main.storage.contentCategory').$request->get('slug').'.blade.php', $content);
            }else {
                Storage::delete(config('main.storage.contentCategory').$request->get('slug').'.blade.php');
            }
            $enContent          = $request->get('en_content') ?? null;
            $enContent          = ImageController::replaceImageInContentWithLoading($enContent);
            if(!empty($enContent)) {
                Storage::put(config('main.storage.enContentCategory').$request->get('en_slug').'.blade.php', $enContent);
            }else {
                Storage::delete(config('main.storage.enContentCategory').$request->get('en_slug').'.blade.php');
            }
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idCategory)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idCategory,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* insert gallery và lưu CSDL */
            if($request->hasFile('gallery')&&!empty($idCategory)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idCategory,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                GalleryController::upload($request->file('gallery'), $params);
            }
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Category!'
            ];
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.category.view', ['id' => $idCategory]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Category::select('*')
                                ->where('id', $id)
                                ->with('seo', 'en_seo', 'products', 'blogs')
                                ->first();
                /* xóa ảnh đại diện trong thư mục */
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* delete content */
                Storage::delete(config('main.storage.contentCategory').$request->get('slug').'.blade.php');
                /* xóa bảng products */
                $info->products()->delete();
                /* xóa relation_style_info_category_info_blog */
                $info->blogs()->delete();
                /* delete bảng seo của product_info */
                $info->seo()->delete();
                $info->en_seo()->delete();
                /* xóa product_info */
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
