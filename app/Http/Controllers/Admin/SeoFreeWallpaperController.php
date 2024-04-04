<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

use Intervention\Image\ImageManagerStatic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Services\BuildInsertUpdateModel;
use App\Models\FreeWallpaper;
use App\Helpers\Charactor;
use App\Models\Category;
use App\Models\RelationSeoFreeWallpaperInfo;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\SeoContent;
use App\Models\Prompt;
use App\Http\Requests\SeoFreeWallpaperRequest;
use App\Http\Controllers\Admin\FreeWallpaperController;

class SeoFreeWallpaperController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $params             = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewSeoFreeWallpaperInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = FreeWallpaper::getList($params);
        $total              = $list->total();
        $categories         = Category::all();
        return view('admin.seoFreeWallpaper.list', compact('list', 'categories', 'total', 'params', 'viewPerPage'));
    }

    public static function view(Request $request){
        $keyTable           = 'free_wallpaper_info';
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? null;
        /* chức năng copy source */
        $idSeoSourceToCopy  = $request->get('id_seo_source') ?? 0;
        $itemSourceToCopy   = FreeWallpaper::select('*')
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
        $item               = FreeWallpaper::select('*')
                                ->where('id', $id)
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
        $prompts    = Prompt::select('*')
                ->where('reference_table', $keyTable)
                ->get();
        $parents    = Category::all();
        $categories = $parents;
        /* tag name */
        $tags           = Tag::all();
        $arrayTag       = [];
        foreach($tags as $tag) $arrayTag[] = $tag->seo->title;
        /* trang canonical -> cùng là sản phẩm */
        $idProduct          = $item->id ?? 0;
        $sources            = FreeWallpaper::select('*')
                                ->whereHas('seos.infoSeo', function($query) use($language){
                                    $query->where('language', $language);
                                })
                                ->where('id', '!=', $idProduct)
                                ->get();
        /* type */
        $type               = !empty($itemSeo) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.seoFreeWallpaper.view', compact('item', 'itemSeo', 'itemSourceToCopy', 'itemSeoSourceToCopy', 'prompts', 'type', 'language', 'sources', 'parents', 'arrayTag', 'categories', 'message'));
    }

    public function createAndUpdate(SeoFreeWallpaperRequest $request){
        try {
            DB::beginTransaction();

            /* ngôn ngữ */
            $keyTable           = 'free_wallpaper_info';
            $idSeo              = $request->get('seo_id');
            $idFreeWallpaper    = $request->get('free_wallpaper_info_id');
            $language           = $request->get('language');
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* update page */
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $keyTable, []);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo);
            }

            /* xử lý riêng cho bảng việt (gốc) */
            if($language=='vi'){
                /* lưu categories */
                FreeWallpaperController::saveCategories($idFreeWallpaper, $request->all());
                /* lưu tag name */
                if(!empty($request->get('tag'))) FreeWallpaperController::createOrGetTagName($idFreeWallpaper, $request->get('tag'));
                /* chỉ có update free_wallpaper_info => vì trong controller này bên ngoài không có tạo */
                FreeWallpaper::updateItem($idFreeWallpaper, [
                    'seo_id' => $idSeo
                ]);
            }

            /* relation_seo_free_wallpaper_info */
            $relationSeoTagInfo = RelationSeoFreeWallpaperInfo::select('*')
                                    ->where('seo_id', $idSeo)
                                    ->where('free_wallpaper_info_id', $idFreeWallpaper)
                                    ->first();
            if(empty($relationSeoTagInfo)) RelationSeoFreeWallpaperInfo::insertItem([
                'seo_id'        => $idSeo,
                'free_wallpaper_info_id'   => $idFreeWallpaper
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
                'message'   => '<strong>Thành công!</strong> Đã cập nhật Hình ảnh!'
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
        return redirect()->route('admin.seoFreeWallpaper.view', ['id' => $idFreeWallpaper, 'language' => $language]);
    }

    public static function autoInput($dataAdd, $type = 'insert'){
        if(!empty($dataAdd)){
            $dataAdd['en_name'] = Charactor::translateViToEn($dataAdd['name']);
            $dataAdd['en_seo_description'] = Charactor::translateViToEn($dataAdd['seo_description']);
            if($type=='insert'){
                $dataAdd['rating_aggregate_count'] = rand(200, 10000);
                $dataAdd['rating_aggregate_star'] = '4.'.rand(5,8);
                $dataAdd['slug']    = Charactor::convertStrToUrl($dataAdd['name']).'-'.time();
                $dataAdd['en_slug'] = Charactor::convertStrToUrl($dataAdd['en_name']).'-'.time();
            }
        }
        return $dataAdd;
    }
}
