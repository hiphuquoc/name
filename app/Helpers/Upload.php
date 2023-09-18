<?php

namespace App\Helpers;

use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use Illuminate\Support\Facades\Storage;

class Upload {
    public static function uploadThumnail($requestImage, $name = null){
        $result             = [];
        if(!empty($requestImage)){
            /* folder upload */
            $folderUpload   = config('image.folder_upload');
            /* image upload */
            $image          = $requestImage;
            $extension      = config('image.extension');
            $name           = $name ?? time();
            /* lấy width và height của ảnh truyền vào để tính percenter resize */
            $imageTmp           = ImageManagerStatic::make($image);
            $percentPixel       = $imageTmp->width()/$imageTmp->height();
            /* save image resize (Normal) */
            $widthImageNormal   = config('image.resize_normal_width');
            $heightImageNormal  = $widthImageNormal/$percentPixel;
            $filenameNormal     = $folderUpload.$name.'.'.$extension;
            ImageManagerStatic::make($image->getRealPath())
                ->encode($extension, config('image.quality'))
                ->resize($widthImageNormal, $heightImageNormal)
                ->save(Storage::path($filenameNormal));
            $result['filePathNormal']    = $filenameNormal;
        }
        return $result;
    }

    public static function uploadCustom($requestImage, $name = null){
        $result             = null;
        if(!empty($requestImage)){
            // ===== folder upload
            $folderUpload   = config('image.folder_upload');
            // ===== image upload
            $image          = $requestImage;
            $extension      = config('image.extension');
            // ===== set filename & checkexists
            $name           = $name ?? time();
            $filename       = $name.'-'.time().'.'.$extension;
            $fileUrl        = $folderUpload.$filename;
            // save image resize
            ImageManagerStatic::make($image->getRealPath())
                ->encode($extension, config('image.quality'))
                ->save(Storage::path($fileUrl));
            $result         = $fileUrl;
        }
        return $result;
    }

    public static function uploadWallpaper($requestImage, $filename = null){
        $result             = null;
        if(!empty($requestImage)){
            // ===== folder upload
            $folderUpload   = config('image.folder_upload');
            // ===== image upload
            $image          = $requestImage;
            // ===== set filename & checkexists
            $fileUrl        = $folderUpload.$filename;
            // save image resize
            ImageManagerStatic::make($image->getRealPath())
                ->encode(config('image.extension'), config('image.quality'))
                ->save(Storage::path($fileUrl));
            $result         = $fileUrl;
            // resize thêm một bản có width = 50px để làm ảnh mờ
            $filenameNotExtension = pathinfo($filename)['filename'];
            $extension      = pathinfo($filename)['extension'];
            $fileUrlMini    = $folderUpload.$filenameNotExtension.'-mini.'.$extension;
            $imageTmp           = ImageManagerStatic::make($image);
            $percentPixel       = $imageTmp->width()/$imageTmp->height();
            $widthImageNormal   = 50;
            $heightImageNormal  = $widthImageNormal/$percentPixel;
            ImageManagerStatic::make($image->getRealPath())
                ->encode($extension, config('image.quality'))
                ->resize($widthImageNormal, $heightImageNormal)
                ->save(Storage::path($fileUrlMini));
        }
        return $result;
    }

    
}