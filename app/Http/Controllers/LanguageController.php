<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Admin\CookieController;

class LanguageController extends Controller {

    public static function set($type = null){
        $flag       = false;
        if(!empty($type)){
            CookieController::set('language', $type, 86400);
            $flag   = true;
        }
        return $flag;
    }

}
