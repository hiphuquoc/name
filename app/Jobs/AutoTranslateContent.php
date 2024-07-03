<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Seo;
use App\Models\Category;
use App\Models\RelationSeoCategoryInfo;
use App\Models\SeoContent;
use App\Models\Prompt;
use App\Models\JobAutoTranslate;
use App\Http\Controllers\Admin\ChatGptController;
use App\Models\JobAutoTranslateLinks;

class AutoTranslateContent implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $content;
    private $language;
    private $idSeo;
    private $idPrompt;

    public function __construct($contentViDB, $language, $idSeo, $idPrompt){
        $this->content  = $contentViDB;
        $this->language = $language;
        $this->idSeo    = $idSeo;
        $this->idPrompt = $idPrompt;
    }

    public function handle(){
        /* xóa các khoảng trắng bị chuyển sang ký tự đặc biệt (do trinh soạn thảo Tyni) */
        $stringContent      = str_replace("\u{A0}", ' ', $this->content->content);
        /* lấy info prompt và prompt text */
        $infoPrompt         = Prompt::select('*')
                                ->where('id', $this->idPrompt)
                                ->first();
        $languageName       = config('language.'.$this->language.'.name');
        $languageCode       = config('language.'.$this->language.'.code');
        $promptText         = str_replace(['#language', '#code'], [$languageName, $languageCode], $infoPrompt->reference_prompt);
        /* dịch content */
        $arrayPartContent   = \App\Helpers\Charactor::splitString($stringContent, 4000);
        $resultContent      = '';
        foreach($arrayPartContent as $contentPart){
            $promptUse      = $promptText . "\n\n" . $contentPart;
            $response       = ChatGptController::callApi($promptUse, $infoPrompt);
            $resultContent  .= $response['content'];
        }
        if(!empty($resultContent)){
            /* thay internal link */
            preg_match_all('/href=["\'](.*?)["\']/', $resultContent, $match);
            $allLink    = !empty($match[1]) ? $match[1] : [];
            $response   = self::translateSlugBySlugOnData($allLink, $this->language, $resultContent);
            $resultContent = $response['content'];
            /* xóa box content cũ và lưu cơ sở dữ liệu */
            SeoContent::select('*')
                ->where('seo_id', $this->idSeo)
                ->where('ordering', $this->content->ordering)
                ->delete();
            SeoContent::insertItem([
                'seo_id'    => $this->idSeo,
                'content'   => $resultContent,
                'ordering'  => $this->content->ordering
            ]);
            /* cập nhật lại trạng thái */
            JobAutoTranslate::where('seo_id', $this->idSeo)
                                ->where('ordering', $this->content->ordering)
                                ->where('language', $this->language)
                                ->update(['status' => 1]);
            /* tạo danh sách links => báo cáo */
            JobAutoTranslateLinks::where('seo_id', $this->idSeo)
                                ->where('ordering', $this->content->ordering)
                                ->where('language', $this->language)
                                ->delete();
            if(!empty($response['array_link'])){
                foreach($response['array_link'] as $l){
                    JobAutoTranslateLinks::insertItem([
                        'seo_id'            => $this->idSeo,
                        'ordering'          => $this->content->ordering,
                        'lanuguage'         => $this->language,
                        'link_source'       => $l['vi'],
                        'link_translate'    => $l['translate']
                    ]);
                }
            }
        }
    }

    private static function translateSlugBySlugOnData($allUrl, $language, $content){
        $responseContent = $content;
        /* tạo array map dịch */
        $arrayMap = [];
        foreach($allUrl as $url){
            $tmp    = [];
            /* kiểm tra xem slug có phải liên kết nội bộ không => liên kết nội bộ bắt đầu bằng tên miền hoặc ../../ */
            $appUrl = preg_quote(env('APP_URL'), '/').'\/';
            $pattern = '/^(' . $appUrl . '|..\/..\/)/';
            preg_match($pattern, $url, $match);
            /* tiến hành tìm link ngôn ngữ và thay thế */
            if(!empty($content)&&!empty($match[1])){
                /* tách ra để có được slug_full tương tự định dạng trong CSDL */
                $slugVi = self::processString($url);
                /* ===== tìm trong CSDL dể lấy ra slug_full ngôn ngữ tương ứng */
                /* lấy tất cả url tiếng việt trên toàn hệ thống */
                $allSeo = Seo::select('*')
                            ->where('language', 'vi')
                            ->get();
                $slugTranslate  = '';
                if(!empty($slugVi)){ /* không phải trang chủ */
                    foreach($allSeo as $seo){
                        if($slugVi==$seo->slug_full){
                            /* tìm link theo ngôn ngữ */
                            $slugTranslate = self::findSlugByLanguage($seo, $language);
                            break;
                        }
                    }
                }
                /* ===== thay thế 2 lần => để tránh việc hinh-nen-dien-thoai thay cho hinh-nen-dien-thoai/url-con */
                /* thay thế slug vào url */
                if(!empty($slugTranslate)){
                    $urlTranslate = str_replace($slugVi, $slugTranslate, $url);
                }else {
                    $urlTranslate = $url.$language;
                }
                /* tạo array */
                $tmp['vi'] = $url;
                $tmp['translate'] = $urlTranslate;
                $arrayMap[] = $tmp;
            }
        }
        /* lọc bỏ các phần tử trùng giá trị gốc và giá trị dịch trong arrayMap */
        $arrayMapUse = [];
        $i  = 1;
        foreach($arrayMap as $itemMap){
            if($i==1){
                $arrayMapUse[] = $itemMap;
            }else {
                $flag   = false;
                foreach($arrayMapUse as $itemMapUse) if($itemMap['vi']==$itemMapUse['vi']&&$itemMap['translate']==$itemMapUse['translate']) $flag = true;
                if($flag==false) $arrayMapUse[] = $itemMap;
            }
            ++$i;
        }
        /* tiến hành thay link */
        foreach ($arrayMapUse as $itemUse) {
            $vi = $itemUse['vi'];
            $translate = $itemUse['translate'];
            // Thay thế href="{{ vi }}"
            $responseContent = str_replace('href="' . $vi . '"', 'href="' . $translate . '"', $responseContent);
            // Thay thế href='{{ vi }}'
            $responseContent = str_replace("href='" . $vi . "'", "href='" . $translate . "'", $responseContent);
        }
        $response['content']    = $responseContent;
        $response['array_link'] = $arrayMap;
        return $response;
    }

    private static function processString($input) {
        // Lấy giá trị từ biến môi trường và thoát các ký tự đặc biệt cho biểu thức chính quy
        $appUrl = preg_quote(env('APP_URL'), '/').'\/';
        // Xóa bỏ "https://name.com.vn/" hoặc "../../" ở đầu chuỗi
        $pattern = '/^(' . $appUrl . '|..\/..\/)/';
        $processedString = preg_replace($pattern, '', $input);
        // Xóa bỏ phần hashtag và biến trong URL
        $processedString = preg_replace('/[#?].*/', '', $processedString);
        // Xóa bỏ dấu "/" ở cuối chuỗi
        $processedString = rtrim($processedString, '/');
        return $processedString;
    }

    private static function findSlugByLanguage($infoSeoVi, $language){
        $slugTranslate = null;
        switch ($infoSeoVi->type) {
            case 'category_info':
                $tmp    = RelationSeoCategoryInfo::select('*')
                            ->where('seo_id', $infoSeoVi->id)
                            ->first();
                $id     = $tmp->category_info_id ?? 0;
                $info   = Category::select('*')
                            ->where('id', $tmp->category_info_id)
                            ->with('seos.infoSeo', function($query) use($language){
                                $query->where('language', $language);
                            })
                            ->first();
                
                if(!empty($info)){
                    foreach($info->seos as $s){
                        if(!empty($s->infoSeo)){
                            $slugTranslate = $s->infoSeo->slug_full;
                            break;
                        }
                    }
                }
                break;
            
            default:
                # code...
                break;
        }
        return $slugTranslate;
    }

}
