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
use App\Models\RelationSeoEnSeo;
use App\Models\Prompt;
use App\Models\Page;
use App\Models\PageType;
use App\Models\RelationSeoPageInfo;
use App\Models\SeoContent;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function createAndUpdate(PageRequest $request){
        try {
            DB::beginTransaction();           
            /* ngôn ngữ */
            $keyTable           = 'page_info';
            $idSeo              = $request->get('seo_id');
            $idPage             = $request->get('page_info_id');
            $language           = $request->get('language');
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
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo);
            }
            /* insert hoặc update page_info */
            $showSidebar        = !empty($request->get('show_sidebar'))&&$request->get('show_sidebar')=='on' ? 1 : 0;
            $tag                = [
                'type_id'       => $request->get('type_id'),
                'show_sidebar'  => $showSidebar
            ];
            if(empty($idPage)){ /* check xem create page hay update page */
                $idPage          = Page::insertItem($tag);
            }else {
                Page::updateItem($idPage, $tag);
            }
            /* relation_seo_page_info */
            $relationSeoTagInfo = RelationSeoPageInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('page_info_id', $idPage)
                                    ->first();
            if(empty($relationSeoTagInfo)) RelationSeoPageInfo::insertItem([
                'seo_id'        => $idSeo,
                'page_info_id'   => $idPage
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
        return redirect()->route('admin.page.view', ['id' => $idPage, 'language' => $language]);
    }

    public static function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* tìm theo ngôn ngữ */
        $item               = Page::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo')
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
                ->where('reference_table', 'page_info')
                ->get();
        $parents            = Page::all();
        /* type */
        $type               = !empty($itemSeo) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        /* type_page */
        $pageTypes          = PageType::all();
        return view('admin.page.view', compact('item', 'itemSeo', 'prompts', 'type', 'language', 'parents', 'message', 'pageTypes'));
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

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Page::select('*')
                                ->where('id', $id)
                                ->with(['files' => function($query){
                                    $query->where('relation_table', 'seo.type');
                                }])
                                ->with('seo')
                                ->first();
                /* xóa ảnh đại diện trong thư mục */ 
                $imageSmallPath     = Storage::path(config('admin.images.folderUpload').basename($info->seo->image_small));
                if(file_exists($imageSmallPath)) @unlink($imageSmallPath);
                $imagePath          = Storage::path(config('admin.images.folderUpload').basename($info->seo->image));
                if(file_exists($imagePath)) @unlink($imagePath);
                /* delete relation */
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
