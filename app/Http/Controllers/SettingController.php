<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use App\Models\Seo;

class SettingController extends Controller {

    public static function settingLanguage($language = 'vi'){
        Session::put('language', $language);
        return true;
    }

    public static function getLanguage(){
        $language   = request()->session()->get('language') ?? null;
        /* trường hợp truy cập lần đầu chưa ghi session -> xác định language thông qua slug */
        if(empty($language)){
            $referer = request()->headers->get('referer');
            if ($referer) {
                /* Lấy đường dẫn cuối cùng của referer */
                $urlParts   = explode('/', $referer);
                $slug       = end($urlParts) ?? null;
                $language   = self::getLanguageBySlug($slug);
            } else {
                $language   = 'vi';
            }
        }
        return $language;
    }

    public static function getLanguageBySlug($slug){
        $language = 'vi';
        if(!empty($slug)){
            $infoPage   = Seo::select('language')
                                ->where('slug', $slug)
                                ->first();
            $language   = $infoPage->language ?? 'vi';
        }
        return $language;
    }

}
