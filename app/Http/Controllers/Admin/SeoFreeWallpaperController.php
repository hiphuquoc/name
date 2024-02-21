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
use App\Models\FreeWallpaperContent;
use App\Models\Tag;
use App\Models\Seo;
use App\Models\EnSeo;
use App\Models\RelationSeoEnSeo;
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
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $item               = FreeWallpaper::select('*')
                                ->where('id', $id)
                                ->with('seo', 'en_seo', 'categories', 'tags')
                                ->first();
        $categories         = Category::all();
        /* gộp lại thành parents và lọc bỏ page hinh-nen-dien-thoai */
        $parents            = $categories;
        /* tag name */
        $tags           = Tag::all();
        $arrayTag       = [];
        foreach($tags as $tag) $arrayTag[] = $tag->name;
        /* type */
        $type               = !empty($item->seo) ? 'edit' : 'create';
        $type               = $request->get('type') ?? $type;
        return view('admin.seoFreeWallpaper.view', compact('item', 'type', 'categories', 'arrayTag', 'parents', 'message'));
    }

    public function update(SeoFreeWallpaperRequest $request){
        try {
            DB::beginTransaction();
            $keyTable           = 'free_wallpaper_info';
            $idFreeWallpaper    = $request->get('free_wallpaper_info_id');
            $seoId              = $request->get('seo_id') ?? 0;
            $enSeoId            = $request->get('en_seo_id') ?? 0;
            /* insert page */
            $dataAdd            = $request->all();
            $type               = !empty($seoId) ? 'update' : 'insert';
            $dataAdd            = self::autoInput($dataAdd, $type);
            $insertSeo          = $this->BuildInsertUpdateModel->buildArrayTableSeo($dataAdd, $keyTable, []);
            $insertEnSeo        = $this->BuildInsertUpdateModel->buildArrayTableEnSeo($dataAdd, $keyTable, []);
            if(!empty($enSeoId)){
                Seo::updateItem($seoId, $insertSeo);
                EnSeo::updateItem($enSeoId, $insertEnSeo);
            }else {
                $seoId = Seo::insertItem($insertSeo);
                $enSeoId = EnSeo::insertItem($insertEnSeo);
            }
            /* kết nối bảng vi và en */
            RelationSeoEnSeo::select('*')
                ->where('seo_id', $seoId)
                ->where('en_seo_id', $enSeoId)
                ->delete();
            RelationSeoEnSeo::insertItem([
                'seo_id'    => $seoId,
                'en_seo_id' => $enSeoId
            ]);
            /* cập nhật lại free_wallpaper_info */
            FreeWallpaper::updateItem($idFreeWallpaper, [
                'name'          => $dataAdd['name'],
                'en_name'       => $dataAdd['en_name'],
                'description'   => $dataAdd['description'] ?? null,
                'seo_id'        => $seoId,
                'en_seo_id'     => $enSeoId
            ]);
            /* insert product_content */
            FreeWallpaperContent::select('*')
                ->where('free_wallpaper_info_id', $idFreeWallpaper)
                ->delete();
            if(!empty($request->get('contents'))){
                foreach($request->get('contents') as $content){
                    if(!empty($content['name'])&&!empty($content['content'])){
                        FreeWallpaperContent::insertItem([
                            'free_wallpaper_info_id'    => $idFreeWallpaper,
                            'name'                      => $content['name'],
                            'content'                   => $content['content'],
                            'en_name'                   => $content['en_name'],
                            'en_content'                => $content['en_content']
                        ]);
                    }
                }
            }
            /* lưu categories */
            FreeWallpaperController::saveCategories($idFreeWallpaper, $request->all());
            /* lưu tag name */
            if(!empty($request->get('tag'))) FreeWallpaperController::createOrGetTagName($idFreeWallpaper, $request->get('tag'));

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
        return redirect()->route('admin.seoFreeWallpaper.view', ['id' => $idFreeWallpaper]);
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
