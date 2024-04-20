<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Upload;
use App\Http\Requests\CategoryBlogRequest;
use App\Models\CategoryBlog;
use App\Models\Seo;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\DB;

class CategoryBlogController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = CategoryBlog::getTreeCategory();
        return view('admin.categoryBlog.list', compact('list', 'params'));
    }

    public function view(Request $request){
        $parents        = CategoryBlog::select('*')
                            ->with('seo')
                            ->get();
        $id             = $request->get('id') ?? 0;
        $item           = CategoryBlog::select('*')
                            ->where('id', $id)
                            ->with('seo')
                            ->first();
        /* type */
        $type           = !empty($item) ? 'edit' : 'create';
        $type           = $request->get('type') ?? $type;
        return view('admin.categoryBlog.view', compact('parents', 'item', 'type'));
    }

    public function create(CategoryBlogRequest $request){
        try {
            DB::beginTransaction();
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            /* insert seo */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'category_blog_info', $dataPath);
            $seoId              = Seo::insertItem($insertSeo);
            /* insert category_blog_info */
            $insertCategory     = $this->BuildInsertUpdateModel->buildArrayTableCategoryBLogInfo($request->all(), $seoId);
            $idCategoryBlog         = CategoryBlog::insertItem($insertCategory);
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Dã tạo Chuyên mục mới'
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
        return redirect()->route('admin.categoryBlog.view', ['id' => $idCategoryBlog]);
    }

    public function update(CategoryBlogRequest $request){
        try {
            DB::beginTransaction();
            $idCategoryBlog         = $request->get('category_blog_info_id');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $updateSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'category_blog_info', $dataPath);
            Seo::updateItem($request->get('seo_id'), $updateSeo);
            /* update category */
            $updateCategory     = $this->BuildInsertUpdateModel->buildArrayTableCategoryBLogInfo($request->all(), $request->get('seo_id'));
            CategoryBlog::updateItem($idCategoryBlog, $updateCategory);
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Các thay đổi đã được lưu'
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
        return redirect()->route('admin.categoryBlog.view', ['id' => $idCategoryBlog]);
    }

    /* vì category đa cấp nên không dùng tính năng delete này */
    // public function delete(Request $request){
    //     if(!empty($request->get('id'))){
    //         try {
    //             DB::beginTransaction();
    //             $id         = $request->get('id');
    //             $info       = CategoryBlog::select('*')
    //                             ->where('id', $id)
    //                             ->with('seo')
    //                             ->first();
    //             /* delete bảng seo */
    //             Seo::find($info->seo->id)->delete();
    //             /* xóa ảnh đại diện trong thư mục */
    //             $imageSmallPath     = Storage::path($info->seo->image_small);
    //             if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
    //             $imagePath          = Storage::path($info->seo->image);
    //             if(file_exists($imagePath)) @unlink($imagePath);
    //             /* xóa category_blog_info */
    //             $info->delete();
    //             DB::commit();
    //             return true;
    //         } catch (\Exception $exception){
    //             DB::rollBack();
    //             return false;
    //         }
    //     }
    // }
}
