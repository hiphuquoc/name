<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class CacheController extends Controller {

    public static function clear(){
        $disk = Storage::disk('gcs');
        $folderSave = config('main_'.env('APP_NAME').'.cache.folderSave');
        
        // Lấy danh sách tất cả file trong thư mục cache
        $files = $disk->files($folderSave);
        
        // Xóa từng file
        foreach($files as $file){
            $disk->delete($file);
        }
        
        return true;
    }

}
