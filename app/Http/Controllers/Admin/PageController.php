<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Helpers\Upload;
use App\Http\Requests\PageRequest;
use App\Models\Seo;
use App\Models\EnSeo;
use App\Models\RelationSeoEnSeo;
use App\Models\Page;
use App\Models\PageType;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function create(PageRequest $request){
        try {
            DB::beginTransaction();
            $keyTable           = 'page_info';
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
            /* insert page_info */
            $showSidebar        = !empty($request->get('show_sidebar'))&&$request->get('show_sidebar')=='on' ? 1 : 0;
            $idPage             = Page::insertItem([
                'seo_id'        => $seoId,
                'en_seo_id'     => $enSeoId,
                'type_id'       => $request->get('type_id'),
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'en_name'       => $request->get('en_name'),
                'en_description'=> $request->get('en_description'),
                'show_sidebar'  => $showSidebar
            ]);
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idPage)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idPage,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* lưu content vào file */
            Storage::put(config('main.storage.contentPage').$request->get('slug').'.blade.php', $request->get('content'));
            Storage::put(config('main.storage.enContentPage').$request->get('en_slug').'.blade.php', $request->get('en_content'));
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Dã tạo Trang mới'
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
        return redirect()->route('admin.page.view', ['id' => $idPage]);
    }


    public function update(PageRequest $request){
        try {
            DB::beginTransaction();
            $seoId              = $request->get('seo_id');
            $enSeoId            = $request->get('en_seo_id');
            $idPage             = $request->get('page_info_id');
            $keyTable           = 'page_info';
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
            /* insert page_info */
            $showSidebar        = !empty($request->get('show_sidebar'))&&$request->get('show_sidebar')=='on' ? 1 : 0;
            $arrayUpdate        = [
                'seo_id'        => $seoId,
                'en_seo_id'     => $enSeoId,
                'type_id'       => $request->get('type_id'),
                'name'          => $request->get('name'),
                'description'   => $request->get('description'),
                'en_name'       => $request->get('en_name'),
                'en_description'=> $request->get('en_description'),
                'show_sidebar'  => $showSidebar
            ];
            Page::updateItem($idPage, $arrayUpdate);
            /* insert slider và lưu CSDL */
            if($request->hasFile('slider')&&!empty($idPage)){
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $params         = [
                    'attachment_id'     => $idPage,
                    'relation_table'    => $keyTable,
                    'name'              => $name
                ];
                SliderController::upload($request->file('slider'), $params);
            }
            /* lưu content vào file */
            Storage::put(config('main.storage.contentPage').$request->get('slug').'.blade.php', $request->get('content'));
            Storage::put(config('main.storage.enContentPage').$request->get('en_slug').'.blade.php', $request->get('en_content'));
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Trang!'
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
        return redirect()->route('admin.page.view', ['id' => $idPage]);
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $item               = Page::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'page_info');
                                }])
                                ->with('seo', 'en_seo')
                                ->first();
        $idNot              = $item->id ?? 0;
        $parents            = Page::select('*')
                                ->where('id', '!=', $idNot)
                                ->get();
        /* content */
        $content            = null;
        if(!empty($item->seo->slug)){
            $content        = Storage::get(config('main.storage.contentPage').$item->seo->slug.'.blade.php');
        }
        /* en content */
        $enContent          = null;
        if(!empty($item->en_seo->slug)){
            $enContent      = Storage::get(config('main.storage.enContentPage').$item->en_seo->slug.'.blade.php');
        }
        /* type_page */
        $pageTypes          = PageType::all();
        /* type */
        $type               = !empty($item) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.page.view', compact('item', 'type', 'pageTypes', 'content', 'enContent', 'parents', 'message'));
    }

    public static function list(Request $request){
        $params                         = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewPageInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        $list               = Page::getList($params);
        return view('admin.page.list', compact('list', 'viewPerPage', 'params'));
    }

    public static function deleteItem(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Page::select('*')
                                ->where('id', $id)
                                ->with('seo')
                                ->first();
                /* delete bảng seo */
                Seo::find($info->seo->id)->delete();
                /* xóa ảnh đại diện trong thư mục */
                $imageSmallPath     = Storage::path($info->seo->image_small);
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path($info->seo->image);
                if(file_exists($imagePath)) @unlink($imagePath);
                /* xóa category_blog_info */
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
