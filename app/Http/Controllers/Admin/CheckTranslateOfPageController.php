<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\HelperController;
use App\Jobs\CheckTranslateOfPage;
use App\Models\CheckTranslate;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CheckTranslateOfPageController extends Controller {

    public static function list(Request $request){
        $params     = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewCheckTranslateOfPage') ?? 20;
        $params['paginate'] = $viewPerPage;
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list               = CheckTranslate::select('*')
                                ->orderBy('id', 'DESC')
                                ->paginate($params['paginate']);
        return view('admin.report.checkTranslateOfPage', compact('list', 'params', 'viewPerPage'));
    }

    public static function checkTranslateOfPage(Request $request) {
        $arrayChecked   = [];
        $idSeo          = $request->get('seo_id') ?? 0;
        $infoPage       = HelperController::getFullInfoPageByIdSeo($idSeo);
        $arrayNotCheck  = ['vi', 'en'];
        foreach($infoPage->seos as $seo){
            if(!empty($seo->infoSeo->language)&&!in_array($seo->infoSeo->language, $arrayNotCheck)){
                CheckTranslateOfPage::dispatch($seo->infoSeo->id, $seo->infoSeo->language);
                $arrayChecked[] = $seo->infoSeo->language;
            }
        }
        $response = [
            'flag' => true,
            'toast_type' => 'success',
            'toast_title' => 'Thành công!',
            'toast_message' => '👋 Đã gửi yêu cầu kiểm tra thành công <span class="highLight_700">' . count($arrayChecked) . '</span> trang ngôn ngữ.'
        ];
        
        return response()->json($response);
    }
    
}
