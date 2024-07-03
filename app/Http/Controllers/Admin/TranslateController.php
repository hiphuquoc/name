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
use App\Models\RelationTagInfoCategoryBlogInfo;
use App\Models\RelationEnCategoryInfoEnCategoryBlogInfo;
use App\Models\RelationCategoryInfoTagInfo;
use App\Models\RelationSeoCategoryInfo;
use App\Models\RelationSeoTagInfo;
use App\Models\SeoContent;
use App\Jobs\AutoTranslateContent;
use App\Models\JobAutoTranslate;

class TranslateController extends Controller {

    public static function list(Request $request){
        $params     = [];
        // /* Search theo tên */
        // if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        $list = Seo::select('*')
            ->whereHas('jobAutoTranslate', function($query) {
                $query->whereColumn('job_auto_translate.language', 'language');
            })
            ->with(['contents', 'jobAutoTranslatelinks'])
            ->with(['jobAutoTranslate' => function($query) {
                $query->whereColumn('job_auto_translate.language', 'language');
            }])
            ->get();
        return view('admin.report.listAutoTranslateContent', compact('list', 'params'));
    }

    public static function createJob(Request $request){
        $idSeoSource    = $request->get('id_seo_source');
        $idSeo          = $request->get('id_seo');
        $idPrompt       = $request->get('id_prompt');
        $language       = $request->get('language');
        /* kiểm tra đã chạy chưa */
        $infoFlag       = JobAutoTranslate::select('*')
                            ->where('seo_id', $idSeo)
                            ->first();
        if(!empty($infoFlag)){
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Thao tác đã được thực hiện trước đó, xóa lịch sử trong "Báo cáo" => "Tự động dịch" của trang này và thử lại!'
            ];
        }else {
            /* lấy content bảng tiếng việt */
            $contents   = SeoContent::select('*')
                            ->where('seo_id', $idSeoSource)
                            ->get();
            /* duyệt qua từng box content để xử lý */
            foreach($contents as $content){
                /* tạo đánh dấu đang và đã thực hiện tính năng */
                JobAutoTranslate::select('*')
                    ->where('seo_id', $idSeo)
                    ->where('ordering', $content->ordering)
                    ->where('language', $language)
                    ->delete();
                $flag = JobAutoTranslate::insertItem([
                    'seo_id'    => $idSeo,
                    'ordering'  => $content->ordering,
                    'language'  => $language
                ]);
                /* tạo job */
                AutoTranslateContent::dispatch($content, $language, $idSeo, $idPrompt);
            }   
            $message        = [
                'type'      => 'success',
                'message'   => '<strong>Thành công!</strong> Đã gửi yêu cầu dịch tự động!'
            ];
        }
        $request->session()->put('message', $message);
        echo true;
    }

    public static function delete(Request $request){
        if(!empty($request->get('id'))){
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
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }
    
}
