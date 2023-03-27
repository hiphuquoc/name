<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Upload;
use App\Http\Requests\BlogRequest;
use App\Models\CategoryBlog;
use App\Models\Blog;
use App\Models\Seo;
use App\Models\RelationCategoryBlogInfoBlogInfo;
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
        $id             = $request->get('id') ?? 0;
        $item           = Blog::select('*')
                            ->where('id', $id)
                            ->with('seo', 'categories.infoCategory')
                            ->first();
        $parents        = CategoryBlog::all();
        $content        = null;
        if(!empty($item->seo->slug)){
            $content    = Storage::get(config('main.storage.contentBlog').$item->seo->slug.'.blade.php');
        }
        /* type */
        $type           = !empty($item) ? 'edit' : 'create';
        $type           = $request->get('type') ?? $type;
        return view('admin.blog.view', compact('parents', 'item', 'type', 'content'));
    }

    public function create(BlogRequest $request){
        try {
            DB::beginTransaction();
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* insert seo */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'blog_info', $dataPath);
            $seoId              = Seo::insertItem($insertSeo);
            /* insert blog_info */
            $insertBlog         = $this->BuildInsertUpdateModel->buildArrayTableBlogInfo($request->all(), $seoId);
            $idBlog             = Blog::insertItem($insertBlog);
            /* lưu content vào file */
            $content            = $request->get('content') ?? null;
            $content            = ImageController::replaceImageInContentWithLoading($content);
            Storage::put(config('main.storage.contentBlog').$request->get('slug').'.blade.php', $content);
            /* relation category_blog_info và blog_info */
            if(!empty($request->get('category_blog_info_id'))){
                foreach($request->get('category_blog_info_id') as $idCategoryBlog){
                    $insertRelationCategoryBlogInfoBlogInfo    = [
                        'category_blog_info_id' => $idCategoryBlog,
                        'blog_info_id'          => $idBlog
                    ];
                    RelationCategoryBlogInfoBlogInfo::insertItem($insertRelationCategoryBlogInfoBlogInfo);
                }
            }
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Dã tạo Bài viết mới'
            ];
        } catch (\Exception $exception){
            DB::rollBack();
            /* Message */
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        /* ===== END:: check_seo_info */
        $request->session()->put('message', $message);
        return redirect()->route('admin.blog.view', ['id' => $idBlog]);
    }

    public function update(BlogRequest $request){
        try {
            DB::beginTransaction();
            $idBlog             = $request->get('blog_info_id');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $updateSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'blog_info', $dataPath);
            Seo::updateItem($request->get('seo_id'), $updateSeo);
            /* update blog */
            $updateBlog         = $this->BuildInsertUpdateModel->buildArrayTableBlogInfo($request->all(), $request->get('seo_id'));
            Blog::updateItem($idBlog, $updateBlog);
            /* lưu content vào file */
            $content            = $request->get('content') ?? null;
            $content            = ImageController::replaceImageInContentWithLoading($content);
            Storage::put(config('main.storage.contentBlog').$request->get('slug').'.blade.php', $content);
            /* relation category_blog_info và blog_info */
            RelationCategoryBlogInfoBlogInfo::select('*')
                ->where('blog_info_id', $idBlog)
                ->delete();
            if(!empty($request->get('category_blog_info_id'))){
                foreach($request->get('category_blog_info_id') as $idCategoryBlog){
                    $insertRelationCategoryBlogInfoBlogInfo    = [
                        'category_blog_info_id' => $idCategoryBlog,
                        'blog_info_id'          => $idBlog
                    ];
                    RelationCategoryBlogInfoBlogInfo::insertItem($insertRelationCategoryBlogInfoBlogInfo);
                }
            }
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
        // /* ===== START:: check_seo_info */
        // CheckSeo::dispatch($request->get('seo_id'));
        /* ===== END:: check_seo_info */
        $request->session()->put('message', $message);
        return redirect()->route('admin.blog.view', ['id' => $idBlog]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Blog::select('*')
                                ->where('id', $id)
                                ->with('seo')
                                ->first();
                /* xóa ảnh đại diện trong thư mục */
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* delete bảng seo */
                Seo::find($info->seo->id)->delete();
                /* xóa blog_info */
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
