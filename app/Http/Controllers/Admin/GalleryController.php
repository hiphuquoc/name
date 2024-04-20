<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Upload;

use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller {

    public static function upload($arrayImage, $params = null){
        $result     = [];
        if(!empty($arrayImage)){
            // ===== folder upload
            $folderUpload       = config('image.folder_upload');
            $extension          = config('image.extension');
            $name               = $params['name'] ?? time();
            $i                  = 0;
            foreach($arrayImage as $image){
                // ===== set filename & checkexists (Small)
                $filename       = $name.'-gallery-'.time().'-'.$i;
                $filepath       = $folderUpload.$filename.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->encode($extension, config('image.quality'))
                    ->save(Storage::path($filepath));
                $result[$i]['file_url']         = Storage::url($filepath);
                /* cập nhật thông tin CSDL */
                $arrayInsert                    = [];
                $arrayInsert['attachment_id']   = $params['attachment_id'] ?? 0;
                $arrayInsert['relation_table']  = $params['relation_table'] ?? null;
                $arrayInsert['file_name']       = $filename;
                $arrayInsert['file_path']       = $filepath;
                $arrayInsert['file_extension']  = $extension;
                $arrayInsert['file_type']       = 'gallery';
                $idInsert                       = SystemFile::insertItem($arrayInsert);
                $result[$i]['file_id']          = $idInsert;
                /* tính tỉ lệ width và height của ảnh được upload => để resize chính xác */
                $infoPixel          = getimagesize(Storage::path($filepath));
                $percentPixel       = $infoPixel[0]/$infoPixel[1];
                /* resize bản middle */
                $widthImageMiddle   = config('image.resize_large_width');
                $heightImageMiddle  = $widthImageMiddle/$percentPixel;
                $filenameMiddle     = $filename.'-middle';
                $filepathMiddle     = $folderUpload.$filenameMiddle.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->encode($extension, config('image.quality'))
                    ->resize($widthImageMiddle, $heightImageMiddle)
                    ->save(Storage::path($filepathMiddle));
                /* resize bản small */
                $widthImageSmall    = config('image.resize_small_width');
                $heightImageSmall   = $widthImageSmall/$percentPixel;
                $filenameSmall      = $filename.'-small';
                $filepathSmall      = $folderUpload.$filenameSmall.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->encode($extension, config('image.quality'))
                    ->resize($widthImageSmall, $heightImageSmall)
                    ->save(Storage::path($filepathSmall));
                /* resize bản mini */
                $widthImageMini     = config('image.resize_mini_width');
                $heightImageMini    = $widthImageMini/$percentPixel;
                $filenameMini       = $filename.'-mini';
                $filepathMini       = $folderUpload.$filenameMini.'.'.$extension;
                ImageManagerStatic::make($image->getRealPath())
                    ->encode($extension, config('image.quality'))
                    ->resize($widthImageMini, $heightImageMini)
                    ->save(Storage::path($filepathMini));
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
                $infofile       = SystemFile::find($id);
                $filePath       = Storage::path($infofile['file_path']);
                if(file_exists($filePath)) @unlink($filePath);
                /* xóa bản middle */
                $filePathMiddle = Storage::path(config('image.folder_upload').$infofile['file_name'].'-middle.'.$infofile['file_extension']);
                if(file_exists($filePathMiddle)) @unlink($filePathMiddle);
                /* xóa bản small */
                $filePathSmall  = Storage::path(config('image.folder_upload').$infofile['file_name'].'-small.'.$infofile['file_extension']);
                if(file_exists($filePathSmall)) @unlink($filePathSmall);
                /* xóa bản mini */
                $filePathMini   = Storage::path(config('image.folder_upload').$infofile['file_name'].'-mini.'.$infofile['file_extension']);
                if(file_exists($filePathMini)) @unlink($filePathMini);
                /* xóa khỏi CSDL */
                $flag           = SystemFile::removeItem($id);
                DB::commit();
                return $flag;
            } catch(\Exception $exception) {
                DB::rollBack();
                return false;
            }
        }
    }
}
