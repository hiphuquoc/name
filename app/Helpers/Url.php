<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SettingController;
use App\Models\Seo;
use App\Models\EnSeo;

class Url {

    public static function checkUrlExists($slug){
        /* check ngôn ngữ Việt */
        $infoPage           = Seo::select('*')
                                ->where('slug', $slug)
                                ->first();
        if(!empty($infoPage->slug_full)) {
            $infoPage->language = 'vi';
            SettingController::settingLanguage('vi');
            return $infoPage;
        }
        /* check ngôn ngữ Anh */
        $infoPage       = EnSeo::select('*')
                            ->where('slug', $slug)
                            ->first();
        if(!empty($infoPage->slug_full)) {
            $infoPage->language = 'en';
            SettingController::settingLanguage('en');
            return $infoPage;
        }
        /* rỗng */
        return null;
    }

    public static function buildBreadcrumb($slugFull, $language = 'vi'){
        $tmp            = explode('/', $slugFull);
        $result         = new \Illuminate\Database\Eloquent\Collection;
        foreach($tmp as $item){
            if($language=='en'){
                $infoItem   = EnSeo::select('*')
                            ->where('slug', $item)
                            ->first();
            }else {
                $infoItem   = Seo::select('*')
                                ->where('slug', $item)
                                ->first();
            }
            if(empty($infoItem)) return null;
            $result[]   = $infoItem;
        }
        return $result;
    }
}