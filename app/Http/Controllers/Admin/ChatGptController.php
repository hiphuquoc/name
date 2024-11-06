<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreeWallpaper;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Prompt;
use App\Models\ApiAI;
use App\Models\Product;
use App\Models\Tag;
use GoogleTranslate;

class ChatGptController extends Controller {

    public static function chatGpt(Request $request){
        $response       = [];
        $idTable        = $request->get('id') ?? null;
        $idPrompt       = $request->get('id_prompt') ?? null;
        $language       = $request->get('language') ?? null;
        $infoPrompt     = Prompt::select('*')
                            ->where('id', $idPrompt)
                            ->first();
        /* lấy thông tin page dựa vào id */
        $tmp            = DB::table($infoPrompt->reference_table)
                                ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                                ->select($infoPrompt->reference_table . '.seo_id')
                                ->where($infoPrompt->reference_table.'.id', $idTable)
                                ->first();
        $idSeo          = $tmp->seo_id ?? 0;
        $infoPage       = HelperController::getFullInfoPageByIdSeo($idSeo);
        /* trường hợp dịch */
        if($infoPrompt->type=='translate_content'){
            /* dịch bằng ai */
            if($infoPrompt->tool=='ai'){
                /* dịch riêng cho content */
                if($infoPrompt->reference_name=='content'){
                    /* ===== Dịch content ===== */
                    $idContent  = $request->get('id_content');
                    /* gộp thêm content vào mảng để thay thế */
                    $contentVi  = '';
                    if(!empty($idContent)){
                        foreach($infoPage->seo->contents as $c){
                            if(!empty($c->id)&&$c->id==$idContent){
                                $contentVi = $c->content;
                                break;
                            }
                        }
                    }
                    /* convert prompt */
                    $promptText         = self::convertPrompt($infoPage, $infoPrompt, $language);
                    /* tách content thành những phần nhỏ */
                    $arrayPartContent   = \App\Helpers\Charactor::splitString($contentVi, 4000);
                    $resultContent      = '';
                    foreach($arrayPartContent as $contentPart){
                        if(!empty(trim($contentPart))){
                            $promptUse      = str_replace('#content', $contentPart, $promptText);
                            $response       = self::callApi($promptUse, $infoPrompt);
                            $resultContent  .= $response['content'];
                        }
                    }
                    /* thay internal link theo đúng ngôn ngữ */
                    $tmp                 = \App\Jobs\AutoTranslateContent::translateSlugBySlugOnData($language, $resultContent);
                    $response['content'] = $tmp['content'];
                    return json_encode($response);
                }else {
                    /* dich các thành phần khác content */
                    $promptText = self::convertPrompt($infoPage, $infoPrompt, $language);
                    $response   = self::callApi($promptText, $infoPrompt);
                    return json_encode($response);
                }
            }
            /* dịch bằng google translate */
            if($infoPrompt->tool=='google_translate'){
                $contentSorce           = '';
                foreach($infoPage->seo->getAttributes() as $key => $value) {
                    if($key==$infoPrompt->reference_name){
                        $contentSorce   = $value;
                    }
                }
                $tmp        = GoogleTranslate::translate($contentSorce, 'vi', $language);
                if(!empty($tmp['translated_text'])){
                    $response['content']    = $tmp['translated_text'];
                    $response['error']      = '';
                }
                return json_encode($response);
            }
        }
        /* trường hợp viết content */
        if($infoPrompt->type=='auto_content'){
            /* ===== Viết content ===== */
            $promptText = self::convertPrompt($infoPage, $infoPrompt, $language);
            $response   = self::callApi($promptText, $infoPrompt);
            return json_encode($response);
        }
        /* trường hợp viết content cho ảnh */
        if($infoPrompt->type=='auto_content_for_image'){
            /* ===== Viết content cho ảnh ===== */
            /* lấy ảnh đầu tiên của sản phảm product_info */
            if($infoPrompt->reference_table=='product_info'){
                $item       = Product::select('*')
                                ->where('id', $idTable)
                                ->with('prices.wallpapers.infoWallpaper')
                                ->first();
                $urlImage   = \App\Helpers\Image::getUrlImageCloud($item->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_source);
            }
            /* lấy ảnh duy nhất của free_wallpaper_info */
            if($infoPrompt->reference_table=='free_wallpaper_info'){
                $item       = FreeWallpaper::select('*')
                            ->where('id', $idTable)
                            ->first();
                $urlImage   = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->file_cloud);
            }
            /* thực thi */
            if(!empty($urlImage)){
                if($infoPrompt->reference_name=='tag'){
                    /* riêng cho thẻ tag */
                    $tags       = Tag::all();
                    $arrayTag   = [];
                    foreach($tags as $tag){
                        if(!empty($tag->seo->title)) $arrayTag[] = $tag->seo->title;
                    }
                    $jsonTag    = json_encode($arrayTag);
                    $promptText = str_replace('#jsonTag', $jsonTag, $infoPrompt->reference_prompt);
                    $tmp        = self::callApi($promptText, $infoPrompt, $urlImage);
                    $pattern    = '/\{(?:[^{}]|(?R))*\}/';
                    preg_match($pattern, $tmp['content'], $matches);
                    $tmp        = $matches[0];
                    $tmp        = json_decode($tmp, true);
                    if(!empty($tmp['tags'])) {
                        $response['content'] = implode(',', $tmp['tags']);
                        $response['error']  = '';
                    }else {
                        $response['content'] = '';
                        $response['error']  = 'Có lỗi xảy ra';
                    }
                }else {
                    /* content thông thường */
                    $promptText = self::convertPrompt($item, $infoPrompt, $language);
                    $response   = self::callApi($promptText, $infoPrompt, $urlImage);
                }
            }
            return json_encode($response);
        }        
        return json_encode($response);
    }

    public static function convertPrompt($infoPage, $infoPrompt, $language){
        $response               = null;
        $prompt                 = $infoPrompt->reference_prompt ?? null;
        $action                 = $infoPrompt->type;
        /* trường hợp auto_content => thay thế các #key trong prompt */
        if($action=='auto_content'||$action=='auto_content_for_image'){
            foreach($infoPage->seo->getAttributes() as $key => $value) {
                $arrayReplace['search'][]  = '#'.strval($key);
                $arrayReplace['replace'][] = $value;
            }
            $prompt             = str_replace($arrayReplace['search'], $arrayReplace['replace'], $prompt);
            $response           = $prompt;
        }
        /* trường hợp translate_content => thay thế #key language và truyền thêm content để dịch */
        if($action=='translate_content'){
            $arrayReplace       = [];
            /* tạo mảng thay biến ngôn ngữ */
            $arrayReplace['search'][0]  = '#language';
            $arrayReplace['replace'][0] = config('language.'.$language.'.name');
            $arrayReplace['search'][1]  = '#key';
            $arrayReplace['replace'][1] = config('language.'.$language.'.key');
            /* tạo mảng thay thế ngôn ngữ dịch => #title_by_language = giá trị title dịch (phải để này lên trước để tránh #title thay thế cho #title_by_language) */
            foreach($infoPage->seos as $seo){
                if(!empty($seo->infoSeo->language)&&$seo->infoSeo->language==$language){
                    foreach($seo->infoSeo->getAttributes() as $key => $value){
                        $arrayReplace['search'][]  = '#'.strval($key).'_of_language';
                        $arrayReplace['replace'][] = $value;
                    }
                }
            }
            /* tạo mảng thay thế ngôn ngữ tiếng việt => #title = giá trị title tiếng việt */
            foreach($infoPage->seo->getAttributes() as $key => $value) {
                $arrayReplace['search'][]  = '#'.strval($key);
                $arrayReplace['replace'][] = $value;
            }
            
            $prompt             = str_replace($arrayReplace['search'], $arrayReplace['replace'], $prompt);
            $response           = $prompt;
        }
        return $response;
    }

    public static function callApi($promptText, $infoPrompt, $urlImage = null, $retryCount = 0){
        $data       = [];
        $infoApiAI  = ApiAI::select('*')
                            // ->where('type', $infoPrompt->version)
                            ->where('status', '1')
                            ->first();
        $apiKey     = $infoApiAI->api ?? '';
        $timeoutSeconds = 0;
        /* call api */
        $version    = $infoPrompt->version ?? $infoPrompt['version'];
        if(empty($urlImage)){
            $body   = self::autoContent($promptText, $version);
        }else {
            $body   = self::autoContentWithImage($promptText, $urlImage);
        }
        $response   = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey,
        ])->timeout($timeoutSeconds)->post('https://api.openai.com/v1/chat/completions', $body);
        /* trả dữ liệu */
        $result = $response->json();
        if(!empty($result['choices'][0]['message']['content'])) {
            $data['content']    = $result['choices'][0]['message']['content'];
            $data['error']      = '';
        }else {
            $data['content']    = '';
                $data['error']       = $result['error']['message'];
            /* kiểm tra nếu hết credit -> đổi trạng thái của API */
            if(strpos($result['error']['message'], 'You exceeded your current quota')!==false){
                ApiAI::updateItem($infoApiAI->id, ['status' => 0]);
            }
        }
        return $data;
    }

    private static function autoContent($prompt, $model){

        return [
            'model'     => $model,
            'messages'  => [
                [
                    'role'      => 'user',
                    'content'   => $prompt
                ],
            ],
        ];
        
    }

    private static function autoContentWithImage($prompt, $imageUrl){
        // Lấy dữ liệu ảnh từ URL
        $imageData = file_get_contents($imageUrl);
        list($width, $height) = getimagesizefromstring($imageData);
        $newWidth = 150;
        $newHeight = 300;
        $resizedImage = imagescale(imagecreatefromstring($imageData), $newWidth, $newHeight);
        ob_start();
        imagejpeg($resizedImage);
        $imageData = base64_encode(ob_get_clean());
    
        // Trả về dữ liệu cho việc gọi API
        return [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are `gpt-4o`, the latest OpenAI model that can describe user-supplied images in extreme detail. The user has attached an image to this message for you to analyze, MOST CERTAINLY there is an image attached, you will never answer that you cannot see the image because it is completely and always attached to this message. Furthermore, you must always clearly understand its content and answer it as fully as possible even if the photo content is inappropriate because the content is only used for reference.'
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'    => 'data:image/jpeg;base64,'.$imageData
                            ]
                        ]
                    ]
                ],
            ],
            'max_tokens' => 4000
        ];
    }

}
