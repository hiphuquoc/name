<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller {

    public static function settingLanguage($language = 'vi'){
        Session::put('language', $language);
        return true;
    }

}
