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
                /* insert hoặc update page_info */
                $showSidebar        = !empty($request->get('show_sidebar'))&&$request->get('show_sidebar')=='on' ? 1 : 0;
                if(empty($idPage)){ /* check xem create page hay update page */
                    $idPage          = Page::insertItem([
                        'type_id'       => $request->get('type_id'),
                        'show_sidebar'  => $showSidebar,
                        'seo_id'        => $idSeo,
                    ]);
                }else {
                    Page::updateItem($idPage, [
                        'type_id'       => $request->get('type_id'),
                        'show_sidebar'  => $showSidebar
                    ]);
                }
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
            $i      = 1;
            foreach($request->get('content') as $content){
                SeoContent::insertItem([
                    'seo_id'    => $idSeo,
                    'content'   => $content,
                    'ordering'  => $i
                ]);
                ++$i;
            }
            
            DB::commit();
            /* Message */
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Trang!'
            ];
            /* nếu có tùy chọn index => gửi google index */
            if($request->get('index_google')==true) {
                $flagIndex = IndexController::indexUrl($idSeo);
                if($flagIndex==200){
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Trang và Báo Google Index!';
                }else {
                    $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Trang! <span style="color:red;">nhưng báo Google Index lỗi</span>';
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
        return redirect()->route('admin.page.view', ['id' => $idPage, 'language' => $language]);
    }

    public static function view(Request $request){
        $keyTable           = 'page_info';
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* chức năng copy source */
        $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
        $itemSourceToCopy   = Page::select('*')
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
        $item               = Page::select('*')
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
        $parents            = Page::all();
        /* trang canonical -> cùng là sản phẩm */
        $idProduct          = $item->id ?? 0;
        $sources            = Page::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', '!=', $idProduct)
                                ->get();
        /* type */
        $type               = !empty($itemSeo) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        /* type_page */
        $pageTypes          = PageType::all();
        return view('admin.page.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'message', 'pageTypes'));
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
                /* xóa ảnh đại diện trên google_clouds */ 
                Upload::deleteWallpaper($info->seo->image);
                /* delete relation */
                $info->files()->delete();
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
