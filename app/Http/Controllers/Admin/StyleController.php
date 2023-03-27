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
use App\Models\Style;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Models\RelationStyleInfoCategoryBlogInfo;

class StyleController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = Style::getTreeStyle();
        return view('admin.style.list', compact('list', 'params'));
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $item               = Style::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'style_info');
                                }])
                                ->with('seo')
                                ->first();
        $idNot              = $item->id ?? 0;
        $parents            = Style::select('*')
                                ->where('id', '!=', $idNot)
                                ->get();
        /* category blog */
        $categoryBlogs      = CategoryBlog::all();
        /* content */
        $content            = null;
        if(!empty($item->seo->slug)){
            $content        = Storage::get(config('main.storage.contentStyle').$item->seo->slug.'.blade.php');
        }
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.style.view', compact('item', 'type', 'parents', 'categoryBlogs', 'message', 'content'));
    }

    public function create(StyleRequest $request){
        try {
            DB::beginTransaction();
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* insert page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'style_info', $dataPath);
            $seoId              = Seo::insertItem($insertSeo);
            /* upload icon */
            $iconPath           = null;
            if($request->hasFile('icon')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug').'-icon' : time();
                $iconPath       = Upload::uploadCustom($request->file('icon'), $name);
            }
            /* insert style_info */
            $idStyle         = Style::insertItem([
                'seo_id'        => $seoId,
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'icon'          => $iconPath
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
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => 'style_info',
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* insert gallery và lưu CSDL */
            if($request->hasFile('gallery')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => 'style_info',
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
            $idSeo              = $request->get('seo_id');
            $idStyle         = $request->get('style_info_id');
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $dataPath       = Upload::uploadThumnail($request->file('image'), $name);
            }
            /* update page */
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), 'style_info', $dataPath);
            Seo::updateItem($idSeo, $insertSeo);
            /* upload icon */
            $iconPath           = null;
            if($request->hasFile('icon')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug').'-icon' : time();
                $iconPath       = Upload::uploadCustom($request->file('icon'), $name);
            }
            /* insert style_info */
            $arrayUpdate        = [
                'name'          => $request->get('name'),
                'description'   => $request->get('description')
            ];
            if(!empty($iconPath)) $arrayUpdate['icon'] = $iconPath;
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
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => 'style_info',
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* insert gallery và lưu CSDL */
            if($request->hasFile('gallery')&&!empty($idStyle)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idStyle,
                    'relation_table'    => 'style_info',
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
}
