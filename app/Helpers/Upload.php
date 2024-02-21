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
            // /* lấy width và height của ảnh truyền vào để tính percenter resize */
            // $imageTmp           = ImageManagerStatic::make($image);
            // $percentPixel       = $imageTmp->width()/$imageTmp->height();
            /* save image resize (Normal) */
            $widthImageNormal   = 800;
            $heightImageNormal  = 533;
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

    public static function uploadWallpaper($requestImage, $filename, $folderUpload){
        $result = null;
        if (!empty($requestImage)) {
            // ===== folder upload
            $image              = $requestImage;
            // ===== set filename & checkexists
            $filenameNotExtension = pathinfo($filename)['filename'];
            $extension          = pathinfo($filename)['extension'];
            $fileUrl            = $folderUpload . $filename;
            $gcsDisk            = Storage::disk('gcs');
            // Resize and save the main image
            $imageTmp           = ImageManagerStatic::make($image->getRealPath());
            $percentPixel       = $imageTmp->width() / $imageTmp->height();
            $widthImage         = $imageTmp->width();
            $heightImage        = $imageTmp->height();
            $gcsDisk->put($fileUrl, $imageTmp->encode($extension, config('image.quality'))->resize($widthImage, $heightImage)->stream());
            $result             = $fileUrl;
            // Resize and save the large image
            $fileUrlLarge       = $folderUpload . $filenameNotExtension . '-large.' . $extension;
            $widthImageLarge    = 800;
            $heightImageLarge   = $widthImageLarge / $percentPixel;
            $gcsDisk->put($fileUrlLarge, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageLarge, $heightImageLarge)->stream());
            // Resize and save the small image
            $fileUrlSmall       = $folderUpload . $filenameNotExtension . '-small.' . $extension;
            $widthImageSmall    = 500;
            $heightImageSmall   = $widthImageSmall / $percentPixel;
            $gcsDisk->put($fileUrlSmall, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageSmall, $heightImageSmall)->stream());
            // Resize and save the mini image
            $fileUrlMini        = $folderUpload . $filenameNotExtension . '-mini.' . $extension;
            $widthImageMini     = 50;
            $heightImageMini    = $widthImageMini / $percentPixel;
            $gcsDisk->put($fileUrlMini, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageMini, $heightImageMini)->stream());
        }
        return $result;
    }
    
}