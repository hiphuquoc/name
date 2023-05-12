<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\CookieController;

class LanguageController extends Controller {

    public static function set($type = null){
        $flag       = false;
        if(!empty($type)){
            CookieController::set('language', $type, 86400);
            $flag   = true;
        }
        return redirect($_SERVER['HTTP_REFERER']);
    }

}
