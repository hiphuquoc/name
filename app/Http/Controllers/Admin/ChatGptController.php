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
use App\Models\Tag;

class ChatGptController extends Controller {

    public static function chatGpt(Request $request){
        $response       = [];
        $idTable        = $request->get('id') ?? null;
        $idPrompt       = $request->get('id_prompt') ?? null;
        $language       = $request->get('language') ?? null;
        $infoPrompt     = Prompt::select('*')
                            ->where('id', $idPrompt)
                            ->first();
        if($infoPrompt->reference_name=='content'&&$infoPrompt->type=='translate_content'){
            /* ===== Dịch content ===== */
            $items          = DB::table($infoPrompt->reference_table)
                                ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                                ->join('seo_content', 'seo_content.seo_id', '=', 'seo.id')
                                ->select($infoPrompt->reference_table . '.*', 'seo.*', 'seo_content.*')
                                ->where($infoPrompt->reference_table.'.id', $idTable)
                                ->get(); /* get số nhiều vì joih lấy ra nhiều content */
            /* xử lý riêng cho dịch content -> vì nằm trong relation và số nhiều */
            $content        = null;
            foreach($items as $item){
                $prompt     = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
                $tmp        = self::callApi($prompt);
                $content    .= $tmp['content'];
            }
            $response['content']    = $content;
            $response['error']      = '';
        }else if($infoPrompt->type!='auto_content_for_image'){
            /* ===== Viết content ===== */
            $item       = DB::table($infoPrompt->reference_table)
                            ->join('seo', 'seo.id', '=', $infoPrompt->reference_table.'.seo_id')
                            ->select($infoPrompt->reference_table . '.*', 'seo.*')
                            ->where($infoPrompt->reference_table.'.id', $idTable)
                            ->first();
            $prompt     = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
            $response   = self::callApi($prompt);
        }else {
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
                $prompt     = str_replace('#jsonTag', $jsonTag, $infoPrompt->reference_prompt);
                $tmp        = self::callApi($prompt, $urlImage);
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
                $prompt     = self::convertPrompt($item, $infoPrompt, $infoPrompt->reference_name, $language);
                $response   = self::callApi($prompt, $urlImage);
            }
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

    private static function callApi($prompt, $urlImage = null){
        $data       = [];
        $apiKey     = env('CHAT_GPT_API_KEY');
        $timeoutSeconds = 0;
        /* call api */
        if(empty($urlImage)){
            $model  = '4.0';
            $body   = self::autoContent($prompt, $model);
        }else {
            $body   = self::autoContentWithImage($prompt, $urlImage);
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
        }
        return $data;
    }

    private static function autoContent($prompt, $version = '3.5'){
        if($version=='3.5'){
            return [
                'model'     => 'gpt-3.5-turbo-1106',
                'messages'  => [
                    [
                        'role'      => 'user',
                        'content'   => $prompt
                    ],
                ],
            ];
        }else if($version=='4.0'){
            return [
                'model'     => 'gpt-4-0125-preview',
                'messages'  => [
                    [
                        'role'      => 'user',
                        'content'   => $prompt
                    ],
                ],
            ];
        }
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
