<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class CacheController extends Controller {

    public static function clear(){
        $caches = glob(Storage::path(config('main_'.env('APP_NAME').'.cache.folderSave')).'*');
        foreach($caches as $cache){
            if(file_exists($cache)) @unlink($cache);
        }
        return true;
    }

}
