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
            $imageTmp           = ImageManagerStatic::make($requestImage);
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

    public static function uploadAvatar($requestImage, $name = null){
        $result             = null;
        if(!empty($requestImage)){
            // ===== folder upload
            $folderUpload   = config('image.folder_upload');
            // ===== image upload
            $image          = $requestImage;
            $extension      = config('image.extension');
            // ===== set filename & checkexists (Small)
            $name           = $name ?? time();
            $fileName       = $name.'-avatar-500x500.'.$extension;
            $fileUrl        = $folderUpload.$fileName;
            // save image resize (Small)
            ImageManagerStatic::make($image->getRealPath())
                ->encode($extension, config('image.quality'))
                ->resize(500, 500)
                ->save(Storage::path($fileUrl));
            $result         = $fileUrl;
        }
        return $result;
    }

    // public static function uploadLogo($requestImage, $name = null){
    //     $result             = null;
    //     if(!empty($requestImage)){
    //         // ===== folder upload
    //         $folderUpload   = config('image.folder_upload');
    //         // ===== image upload
    //         $image          = $requestImage;
    //         $extension      = config('image.extension');
    //         // ===== set filename & checkexists
    //         $name           = $name ?? time();
    //         $filename       = $name.'-logo-'.config('image.resize_normal_width').'.'.$extension;
    //         $fileUrl        = $folderUpload.$filename;
    //         // save image resize
    //         ImageManagerStatic::make($image->getRealPath())
    //             ->encode($extension, config('image.quality'))
    //             ->resize(660, 660)
    //             ->save(Storage::path($fileUrl));
    //         $result         = $fileUrl;
    //     }
    //     return $result;
    // }

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

    
}