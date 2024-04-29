<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Jobs\BuildScss;
use App\Helpers\Charactor;

class HelperController extends Controller {

    public function convertStrToUrl(Request $request){
        $string     = $request->get('string') ?? null;
        $response   = '';
        if(!empty($string)){
            $response = Charactor::convertStrToUrl($string);
        }
        echo $response;
    }
    
}
