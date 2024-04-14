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
        /* trường hợp dịch */
        if($infoPrompt->type=='translate_content'){
            /* dịch bàng ai */
            if($infoPrompt->tool=='ai'){
                /* xử lý riêng cho content */
                if($infoPrompt->reference_name=='content'){
                    $idContent  = $request->get('id_content');
                    /* ===== Dịch content ===== */
                    $item       = DB::table($infoPrompt->reference_table)
                                    ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                                    ->join('seo_content', 'seo_content.seo_id', '=', 'seo.id')
                                    ->select($infoPrompt->reference_table . '.*', 'seo.*', 'seo_content.id as seo_content_id', 'seo_content.content')
                                    ->where($infoPrompt->reference_table.'.id', $idTable)
                                    ->where('seo_content.id', $idContent)
                                    ->first();
                    /* xử lý riêng cho dịch content -> vì lấy ra idContent */
                    $promptText = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
                    $response   = self::callApi($promptText, $infoPrompt);
                    return json_encode($response);
                }

                /* dich khác content */
                $item       = DB::table($infoPrompt->reference_table)
                                ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                                ->select($infoPrompt->reference_table . '.*', 'seo.*')
                                ->where($infoPrompt->reference_table.'.id', $idTable)
                                ->first();
                $promptText = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
                $response   = self::callApi($promptText, $infoPrompt);
                return json_encode($response);
            }
            /* dịch bằng google translate */
            if($infoPrompt->tool=='google_translate'){
                $item       = DB::table($infoPrompt->reference_table)
                                ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                                ->select($infoPrompt->reference_table . '.*', 'seo.*')
                                ->where($infoPrompt->reference_table.'.id', $idTable)
                                ->first();
                $contentSorce           = null;
                foreach($item as $key => $value){
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
            $item       = DB::table($infoPrompt->reference_table)
                            ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                            ->select($infoPrompt->reference_table . '.*', 'seo.*')
                            ->where($infoPrompt->reference_table.'.id', $idTable)
                            ->first();
            $promptText = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
            $response   = self::callApi($promptText, $infoPrompt);
            return json_encode($response);
        }
        /* trường hợp viết content cho ảnh */
        if($infoPrompt->type=='auto_content_for_image'){
            /* ===== Viết content cho ảnh ===== */
            $item       = FreeWallpaper::select('*')
            ->where('id', $idTable)
            ->first();
            $urlImage   = \App\Helpers\Image::getUrlImageSmallByUrlImage($item->file_cloud);
            if($infoPrompt->reference_name=='tag'){
                /* riêng cho thẻ tag */
                $tags       = Tag::all();
                $arrayTag   = [];
                foreach($tags as $tag){
                    $arrayTag[] = $tag->seo->title;
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
                $promptText = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
                $response   = self::callApi($promptText, $infoPrompt, $urlImage);
            }
            return json_encode($response);
        }        
        return json_encode($response);
    }

    private static function convertPrompt($item, $infoPrompt, $referenceName, $language){
        $response               = null;
        $prompt                 = $infoPrompt->reference_prompt ?? null;
        $action                 = $infoPrompt->type;
        /* trường hợp auto_content => thay thế các #key trong prompt */
        if($action=='auto_content'||$action=='auto_content_for_image'){
            foreach($item as $key => $value){
                $str            = '#'.strval($key);
                $prompt         = str_replace($str, $value, $prompt);
            }
            $response           = $prompt;
        }
        /* trường hợp translate_content => thay thế #key language và truyền thêm content để dịch */
        if($action=='translate_content'){
            $languageName       = config('language.'.$language.'.name');
            $languageCode       = config('language.'.$language.'.code');
            $prompt             = str_replace(['#language', '#code'], [$languageName, $languageCode], $prompt);
            foreach($item as $key => $value){
                if($key==$referenceName){
                    $prompt     = $prompt . "\n\n" . $value;
                    break;
                }
            }
            $response           = $prompt;
        }
        return $response;
    }

    private static function callApi($promptText, $infoPrompt, $urlImage = null, $retryCount = 0){
        $data       = [];
        /* nếu 3.5 thì lấy ngẫu nhiên các phần tử được active */
        if($infoPrompt->version=='gpt-3.5-turbo-1106'){
            $infoApiAI  = ApiAI::select('*')
                            ->where('type', $infoPrompt->version)
                            ->where('status', '1')
                            ->inRandomOrder()
                            ->first();
        }else { /* nếu version 4.0 thì lấy duy nhất phần tử được active */
            $infoApiAI  = ApiAI::select('*')
                            ->where('type', $infoPrompt->version)
                            ->where('status', '1')
                            ->first();
        }
        $apiKey     = $infoApiAI->api ?? '';
        $timeoutSeconds = 0;
        /* call api */
        if(empty($urlImage)){
            $body   = self::autoContent($promptText, $infoPrompt->version);
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
            // /* nếu lỗi vì bất kì nguyên nhân gì sẽ gọi lại API 1 lần */
            // if ($retryCount < 1) {
            //     $retryCount++;
            //     return self::callApi($promptText, $infoPrompt, $urlImage, $retryCount);
            // }else {
            //     $data['content']    = '';
            //     $data['error']       = $result['error']['message'];
            // }
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

        // if($version=='3.5'){
            
        // }else if($version=='4.0'){
        //     return [
        //         'model'     => 'gpt-4-0125-preview',
        //         'messages'  => [
        //             [
        //                 'role'      => 'user',
        //                 'content'   => $prompt
        //             ],
        //         ],
        //     ];
        // }
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
            'model' => 'gpt-4-vision-preview',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are `gpt-4-vision-preview`, the latest OpenAI model that can describe user-supplied images in extreme detail. The user has attached an image to this message for you to analyze, MOST CERTAINLY there is an image attached, you will never answer that you cannot see the image because it is completely and always attached to this message. Furthermore, you must always clearly understand its content and answer it as fully as possible even if the photo content is inappropriate because the content is only used for reference.'
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
            'max_tokens' => 3000
        ];
    }

}
