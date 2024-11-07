<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Upload;
use App\Http\Requests\TagRequest;
use App\Models\Seo;
use App\Models\LanguageTagInfo;
use App\Models\Prompt;
use App\Models\Tag;
use App\Models\Category;
use App\Models\CategoryBlog;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\GalleryController;
use App\Jobs\AutoTranslateAndCreatePage;
use App\Models\RelationEnCategoryInfoEnCategoryBlogInfo;
use App\Models\RelationCategoryInfoTagInfo;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\RelationSeoPageInfo;
use App\Models\RelationSeoProductInfo;
use App\Models\SeoContent;
use App\Jobs\AutoTranslateContent;
use App\Models\JobAutoTranslate;

class TranslateController extends Controller {

    public function viewcreateJobTranslateContent(Request $request){
        return view('admin.translate.viewcreateJobTranslateContent');
    }

    public function viewCreateJobTranslateAndCreatePage(Request $request){
        return view('admin.translate.viewCreateJobTranslateAndCreatePage');
    }

    public static function list(Request $request){
        $params     = [];
        /* paginate */
        $viewPerPage        = Cookie::get('viewTranslateReport') ?? 20;
        $params['paginate'] = $viewPerPage;
        // /* Search theo tên */
        // if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list = Seo::select('*')
            ->whereHas('jobAutoTranslate', function ($query) {
                $query->whereColumn('job_auto_translate.language', 'language');
            })
            ->with(['contents', 'jobAutoTranslatelinks'])
            ->with(['jobAutoTranslate' => function ($query) {
                $query->whereColumn('job_auto_translate.language', 'language');
            }])
            ->paginate($params['paginate']);
        return view('admin.report.listAutoTranslateContent', compact('list', 'params', 'viewPerPage'));
    }

    public static function reRequestTranslate(Request $request){
        $idSeoByLanguage        = $request->get('id_seo');
        $language               = $request->get('language');
        /* xóa báo cáo cũ */
        $request    = new Request(['id' => $idSeoByLanguage]);
        $result     = self::delete($request);
        /* lấy trang theo ngôn ngữ */
        $infoPage               = \App\Http\Controllers\Admin\HelperController::getFullInfoPageByIdSeo($idSeoByLanguage);
        $idSeoVI                = $infoPage->seo->id;
        /* tạo lại */
        $flag       = self::createJobTranslateContent($idSeoVI, $language);
        return response()->json($flag);
    }

    public static function createMultiJobTranslateContent(Request $request){
        $urlVi      = $request->get('url_vi');
        $slug       = self::getSlugByUrl($urlVi);
        /* lấy thông tin trang gốc - vi */
        $tmp        = Seo::select('*')
                        ->where('slug', $slug)
                        ->first();
        $arrayIdSeoRequested = [];
        if(!empty($tmp->id)){
            /* kiểm tra xem loại nào */
            $infoPage =     HelperController::getFullInfoPageByIdSeo($tmp->id);
            /* duyệt sang mảng để tạo yêu cầu */
            if(!empty($infoPage)){
                foreach($infoPage->seos as $seo){
                    if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language!='vi'){
                        $flag = self::createJobTranslateContent($infoPage->seo->id, $seo->infoSeo->language);
                        if($flag==true) $arrayIdSeoRequested[] = $infoPage->seo->id;
                    }
                }
            }
        }
        /* Message */
        $message        = [
            'type'      => 'success',
            'message'   => 'Đã gửi yêu cầu tạo '.count($arrayIdSeoRequested).' trang ngôn ngữ cho Url: '.$urlVi,
        ];
        $request->session()->put('message', $message);
        return redirect()->route('admin.translate.viewcreateJobTranslateContent');
    }

    public static function createJobTranslateContentAjax(Request $request){
        $idSeoVI        = $request->get('id_seo_vi');
        $language       = $request->get('language');
        $flag           = self::createJobTranslateContent($idSeoVI, $language);
        if($flag==true){
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã gửi yêu cầu dịch tự động!'
            ];
        }else {
            $message    = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Thao tác đã được thực hiện trước đó, xóa lịch sử trong "Báo cáo" => "Tự động dịch" của trang này và thử lại!'
            ];
        }
        $request->session()->put('message', $message);
        echo true;
    }

    public static function createJobTranslateContent($idSeoVI, $language){
        $flag                   = false;
        /* lấy trang theo ngôn ngữ */
        $infoPage               = \App\Http\Controllers\Admin\HelperController::getFullInfoPageByIdSeo($idSeoVI);
        $idSeo                  = 0;
        if (!empty($infoPage->seos)) {
            foreach($infoPage->seos as $seo){
                if(!empty($seo->infoSeo->language==$language)) {
                    $idSeo = $seo->infoSeo->id;
                    break;
                }
            }
        }
        if (!empty($idSeo)&&$language!='vi') {
            /* kiểm tra đã chạy chưa */
            $infoFlag   = JobAutoTranslate::select('*')
                ->where('seo_id', $idSeo)
                ->first();
            if (empty($infoFlag)) {
                /* lấy content bảng tiếng việt */
                $contents   = SeoContent::select('*')
                    ->where('seo_id', $idSeoVI)
                    ->get();
                /* duyệt qua từng box content để xử lý */
                foreach ($contents as $content) {
                    /* lấy ordering làm key */
                    $ordering   = $content->ordering;
                    /* tạo đánh dấu đang và đã thực hiện tính năng */
                    JobAutoTranslate::select('*')
                        ->where('seo_id', $idSeo)
                        ->where('ordering', $ordering)
                        ->where('language', $language)
                        ->delete();
                    JobAutoTranslate::insertItem([
                        'seo_id'    => $idSeo,
                        'ordering'  => $ordering,
                        'language'  => $language
                    ]);
                    /* lấy prompt đang được áp dụng cho content */
                    $type       = $infoPage->seo->type;
                    $infoPrompt = Prompt::select('*')
                                    ->where('reference_name', 'content')
                                    ->where('type', 'translate_content')
                                    ->where('reference_table', $type)
                                    ->first();
                    /* tạo job */
                    AutoTranslateContent::dispatch($ordering, $language, $idSeo, $infoPrompt);
                }
                $flag = true;
            }
        }
        return $flag;
    }

    public static function createJobTranslateAndCreatePageAjax(Request $request) { /* function tự động tạo ra các trang ngôn ngữ khác gồm title, seo_title, seo_description, slug */
        $urlVi      = $request->get('url_vi');
        $slug       = self::getSlugByUrl($urlVi);
        /* lấy thông tin trang gốc - vi */
        $tmp        = Seo::select('*')
            ->where('slug', $slug)
            ->first();
        $arrayLanguageRequested  = [];
        if (!empty($tmp->id)) {
            /* kiểm tra xem loại nào */
            $infoPage =     HelperController::getFullInfoPageByIdSeo($tmp->id);
            if (!empty($infoPage)) $arrayLanguageRequested = self::createJobTranslateAndCreatePage($infoPage);
        }
        /* Message */
        $message        = [
            'type'      => 'success',
            'message'   => 'Đã gửi yêu cầu tạo '.count($arrayLanguageRequested).' trang ngôn ngữ cho Url: '.$urlVi,
        ];
        $request->session()->put('message', $message);
        return redirect()->route('admin.translate.viewCreateJobTranslateAndCreatePage');
    }

    public static function createJobTranslateAndCreatePage($infoPage) { /* function tự động tạo ra các trang ngôn ngữ khác gồm title, seo_title, seo_description, slug */
        $arrayLanguageRequested = [];
        if (!empty($infoPage)) {
            /* xây dựng aray của những ngôn ngữ đã có trang */
            $arrayLanguageHas       = [];
            $i                      = 0;
            foreach ($infoPage->seos as $p) {
                if (!empty($p->infoSeo->language)) {
                    $arrayLanguageHas[$i]['key']    = $p->infoSeo->language;
                    $arrayLanguageHas[$i]['title']  = $p->infoSeo->title;
                    $arrayLanguageHas[$i]['seo_title']    = $p->infoSeo->seo_title;
                    $arrayLanguageHas[$i]['seo_description']    = $p->infoSeo->seo_description;
                    $arrayLanguageHas[$i]['slug']    = $p->infoSeo->slug;
                    $arrayLanguageHas[$i]['parent']    = $p->infoSeo->parent;
                    ++$i;
                }
            }
            /* xây dựng array của những ngôn ngữ chưa có trang */
            $arrayLanguageNonHas    = [];
            $i                      = 0;
            foreach (config('language') as $ld) {
                foreach ($arrayLanguageHas as $lh) {
                    $flag           = false;
                    if ($lh['key'] == $ld['key']) {
                        $flag       = true;
                        break;
                    }
                }
                if ($flag == false) {
                    $arrayLanguageNonHas[$i]['key'] = $ld['key'];
                    $arrayLanguageNonHas[$i]['name'] = $ld['name'];
                    $arrayLanguageNonHas[$i]['name_by_language'] = $ld['name_by_language'];
                    ++$i;
                }
            }
            /* phân chia -> mỗi lần thực hiện 5 ngôn ngữ */
            $numberPertime          = 5;
            $chunkedArrays          = array_chunk($arrayLanguageNonHas, $numberPertime);
            foreach ($chunkedArrays as $arrayLanguageTranslate) {
                $flag = AutoTranslateAndCreatePage::dispatch($arrayLanguageHas, $arrayLanguageTranslate, $infoPage);
                if($flag) $arrayLanguageRequested = array_merge($arrayLanguageRequested, $arrayLanguageTranslate);
            }
        }
        return $arrayLanguageRequested;
    }

    public static function getSlugByUrl($url){
        $slug   = '';
        if(!empty($url)){
            $tmp        = explode('/', $url);
            $slug       = $tmp[count($tmp) - 1];
        }
        return $slug;
    }

    public static function redirectEdit(Request $request){
        $language   = $request->get('language');
        $infoPage   = HelperController::getFullInfoPageByIdSeo($request->get('id_seo_by_language'));
        $idPage     = $infoPage->id;
        $type       = $infoPage->seo->type;
        $type       = HelperController::determinePageType($type);
        $tmp        = explode('_', $type);
        $typeRoute  = $tmp[0];
    
        // dd($typeRoute);
        // Tạo URL thay vì redirect
        $url = route('admin.'.$typeRoute.'.view', [
            'id'        => $idPage,
            'language'  => $language,
        ]);
    
        // Trả về URL
        return response()->json(['url' => $url]);
    }

    public static function delete(Request $request) {
        if (!empty($request->get('id'))) {
            try {
                DB::beginTransaction();
                $idSeo      = $request->get('id');
                $info       = Seo::select('*')
                    ->where('id', $idSeo)
                    ->with('jobAutoTranslate', 'jobAutoTranslateLinks')
                    ->first();
                $info->jobAutoTranslate()->delete();
                $info->jobAutoTranslateLinks()->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }
}
