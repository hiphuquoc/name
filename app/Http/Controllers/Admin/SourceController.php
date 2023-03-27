<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Upload;

use Intervention\Image\ImageManagerStatic;
use App\Models\SourceFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SourceController extends Controller {

    public static function upload($arrayImage, $type = 'wallpaper_mobile', $params = null){
        $result     = [];
        if(!empty($arrayImage)){
            // ===== folder upload
            $folderUpload       = config('image.folder_upload');
            $name               = $params['name'] ?? time();
            $i                  = 0;
            foreach($arrayImage as $image){
                // ===== set filename & checkexists (Small)
                $filename       = $name.'-source-'.\App\Helpers\Charactor::randomString(15);
                $extension      = $image->getClientOriginalExtension();
                $filepath       = $folderUpload.$filename.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->save(Storage::path($filepath));
                $result[$i]['file_url']         = Storage::url($filepath);
                /* cập nhật thông tin CSDL */
                $arrayInsert                    = [];
                $arrayInsert['attachment_id']   = $params['attachment_id'] ?? 0;
                $arrayInsert['relation_table']  = $params['relation_table'] ?? null;
                $arrayInsert['file_name']       = $filename;
                $arrayInsert['file_path']       = $filepath;
                $arrayInsert['file_extension']  = $extension;
                $arrayInsert['file_type']       = $type;
                $idInsert                       = SourceFile::insertItem($arrayInsert);
                $result[$i]['file_id']          = $idInsert;
                ++$i;
            }
        }
        return $result;
    }

    public static function remove(Request $request){
        $id         = $request->get('id') ?? 0;
        $flag       = self::actionRemove($id); 
        return $flag;
    }

    public static function removeById($id){
        $id     = $id ?? 0;
        $flag       = self::actionRemove($id); 
        return $flag;
    }

    private static function actionRemove($id){
        if(!empty($id)){
            try {
                DB::beginTransaction();
                /* xóa file */
                $infofile   = SourceFile::find($id);
                $filePath   = Storage::path($infofile['file_path']);
                if(file_exists($filePath)) @unlink($filePath);
                // /* xóa bản small */
                // $filePathSmall  = Storage::path(config('image.folder_upload').$infofile['file_name'].'-small.'.$infofile['file_extension']);
                // if(file_exists($filePathSmall)) @unlink($filePathSmall);
                // /* xóa bản mini */
                // $filePathMini   = Storage::path(config('image.folder_upload').$infofile['file_name'].'-mini.'.$infofile['file_extension']);
                // if(file_exists($filePathMini)) @unlink($filePathMini);
                /* xóa khỏi CSDL */
                $flag       = SourceFile::removeItem($id);
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }
}
