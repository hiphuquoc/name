<?php

namespace App\Http\Controllers;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cookie;

class CookieController extends Controller {

    public function setCsrfFirstTime(){
        if(empty($_COOKIE['XSRF-TOKEN'])){
            Cookie::queue('XSRF-TOKEN', csrf_token(), 86400);
            return json_encode(true);
        }
        return json_encode(false);
    }

}
