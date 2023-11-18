<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\StyleRequest;
use App\Models\Seo;
use App\Models\EnSeo;
use App\Models\Style;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\RelationStyleInfoCategoryBlogInfo;
use App\Models\RelationSeoEnSeo;

class StyleController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = Style::select('*')
                                ->orderBy('id', 'DESC')
                                ->get();
        return view('admin.style.list', compact('list', 'params'));
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $keyTable           = 'style_info';
        $item               = Style::select('*')
                                ->where('id', $id)
                                ->with('seo', 'en_seo')
                                ->first();
        $parents            = Category::select('*')
                                ->get();
        /* category blog */
        $categoryBlogs      = CategoryBlog::all();
        /* content */
        $content            = null;
        if(!empty($item->seo->slug)){
            $content        = Storage::get(config('main.storage.contentStyle').$item->seo->slug.'.blade.php');
        }
        /* en content */
        $enContent          = null;
        if(!empty($item->en_seo->slug)){
            $enContent      = Storage::get(config('main.storage.enContentStyle').$item->en_seo->slug.'.blade.php');
        }
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.style.view', compact('item', 'type', 'parents', 'categoryBlogs', 'message', 'content', 'enContent'));
    }

    public function create(StyleRequest $request){
        try {
            DB::beginTransaction();
            $keyTable           = 'style_info';
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
            /* insert style_info */
            $idStyle         = Style::insertItem([
                'seo_id'        => $seoId,
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'en_seo_id'     => $enSeoId,
                'en_name'       => $request->get('en_name'),
                'en_description'=> $request->get('en_description')
            ]);
            /* insert relation_style_info_category_blog_id */
            if(!empty($request->get('category_blog_info_id'))){
                foreach($request->get('category_blog_info_id') as $idCategoryBlogInfo){
                    RelationStyleInfoCategoryBlogInfo::insertItem([
                        'style_info_id'         => $idStyle,
                        'category_blog_info_id' => $idCategoryBlogInfo
                    ]);
                }
            }
            /* lưu content vào file */
            $content            = $request->get('content') ?? null;
            $content            = ImageController::replaceImageInContentWithLoading($content);
            if(!empty($content)) Storage::put(config('main.storage.contentStyle').$request->get('slug').'.blade.php', $content);
            $enContent          = $request->get('en_content') ?? null;
            $enContent          = ImageController::replaceImageInContentWithLoading($enContent);
            if(!empty($enContent)) Storage::put(config('main.storage.enContentStyle').$request->get('en_slug').'.blade.php', $enContent);
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* insert gallery và lưu CSDL */
            if($request->hasFile('gallery')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                GalleryController::upload($request->file('gallery'), $params);
            }
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Dã tạo Style mới'
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
        return redirect()->route('admin.style.view', ['id' => $idStyle]);
    }

    public function update(StyleRequest $request){
        try {
            DB::beginTransaction();
            $keyTable           = 'style_info';
            $seoId              = $request->get('seo_id');
            $enSeoId            = $request->get('en_seo_id');
            $idStyle            = $request->get('style_info_id');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $updateSeo              = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
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
            /* insert style_info */
            $arrayUpdate        = [
                'seo_id'        => $seoId,
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'en_seo_id'     => $enSeoId,
                'en_name'       => $request->get('en_name'),
                'en_description'=> $request->get('en_description')
            ];
            Style::updateItem($idStyle, $arrayUpdate);
            /* insert relation_style_info_category_blog_id */
            RelationStyleInfoCategoryBlogInfo::select('*')
                ->where('style_info_id', $idStyle)
                ->delete();
            if(!empty($request->get('category_blog_info_id'))){
                foreach($request->get('category_blog_info_id') as $idCategoryBlogInfo){
                    RelationStyleInfoCategoryBlogInfo::insertItem([
                        'style_info_id'         => $idStyle,
                        'category_blog_info_id' => $idCategoryBlogInfo
                    ]);
                }
            }
            /* lưu content vào file */
            $content            = $request->get('content') ?? null;
            $content            = ImageController::replaceImageInContentWithLoading($content);
            if(!empty($content)) {
                Storage::put(config('main.storage.contentStyle').$request->get('slug').'.blade.php', $content);
            }else {
                Storage::delete(config('main.storage.contentStyle').$request->get('slug').'.blade.php');
            }
            $enContent          = $request->get('en_content') ?? null;
            $enContent          = ImageController::replaceImageInContentWithLoading($enContent);
            if(!empty($enContent)) {
                Storage::put(config('main.storage.enContentStyle').$request->get('en_slug').'.blade.php', $enContent);
            }else {
                Storage::delete(config('main.storage.enContentStyle').$request->get('en_slug').'.blade.php');
            }
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* insert gallery và lưu CSDL */
            if($request->hasFile('gallery')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                GalleryController::upload($request->file('gallery'), $params);
            }
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Style!'
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
        return redirect()->route('admin.style.view', ['id' => $idStyle]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Style::select('*')
                                ->where('id', $id)
                                ->with('seo', 'products', 'categoryBlogs')
                                ->first();
                /* xóa ảnh đại diện trong thư mục */
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* xóa bảng products */
                $info->products()->delete();
                /* xóa relation_style_info_category_info_blog */
                $info->categoryBlogs()->delete();
                /* delete bảng seo của product_info */
                $info->seo()->delete();
                /* xóa product_info */
                $info->delete();
                // DB::commit();
                return true;
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }
}
