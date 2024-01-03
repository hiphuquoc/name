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

    public static function uploadWallpaper($requestImage, $filename = null){
        $result = null;
        if (!empty($requestImage)) {
            // ===== folder upload
            $folderUpload       = config('main.google_cloud_storage.wallpapers');
            $image              = $requestImage;
            // ===== set filename & checkexists
            $filenameNotExtension = pathinfo($filename)['filename'];
            $extension          = pathinfo($filename)['extension'];
            $fileUrl            = $folderUpload . $filename;
            $gcsDisk            = Storage::disk('gcs');
            // Resize and save the main image
            $imageTmp           = ImageManagerStatic::make($image->getRealPath());
            $percentPixel       = $imageTmp->width() / $imageTmp->height();
            $widthImage         = 800;
            $heightImage        = $widthImage / $percentPixel;
            $gcsDisk->put($fileUrl, $imageTmp->encode($extension, config('image.quality'))->resize($widthImage, $heightImage)->stream());
            $result             = $fileUrl;
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

    // public static function uploadWallpaper($requestImage, $filename = null){
    //     $result                 = null;
    //     if(!empty($requestImage)){
    //         // ===== folder upload
    //         $folderUpload       = config('image.folder_upload');
    //         // ===== image upload
    //         $image              = $requestImage;
    //         // ===== set filename & checkexists
    //         $fileUrl            = $folderUpload.$filename;
    //         // save image resize
    //         $imageTmp           = ImageManagerStatic::make($image);
    //         $percentPixel       = $imageTmp->width()/$imageTmp->height();
    //         $widthImage         = 800;
    //         $heightImage        = $widthImage/$percentPixel;
    //         ImageManagerStatic::make($image->getRealPath())
    //             ->encode(config('image.extension'), config('image.quality'))
    //             ->resize($widthImage, $heightImage)
    //             ->save(Storage::path($fileUrl));
    //         $result             = $fileUrl;
    //         // resize thêm một bản có width 50px để làm ảnh mini
    //         $filenameNotExtension = pathinfo($filename)['filename'];
    //         $extension          = pathinfo($filename)['extension'];
    //         $fileUrlMini        = $folderUpload.$filenameNotExtension.'-mini.'.$extension;
    //         $widthImageMini     = 50;
    //         $heightImageMini    = $widthImageMini/$percentPixel;
    //         ImageManagerStatic::make($image->getRealPath())
    //             ->encode($extension, config('image.quality'))
    //             ->resize($widthImageMini, $heightImageMini)
    //             ->save(Storage::path($fileUrlMini));
    //         // resize thêm một bản có width 500px để làm ảnh small
    //         $fileUrlSmall       = $folderUpload.$filenameNotExtension.'-small.'.$extension;
    //         $widthImageSmall    = 500;
    //         $heightImageSmall   = $widthImageSmall/$percentPixel;
    //         ImageManagerStatic::make($image->getRealPath())
    //             ->encode($extension, config('image.quality'))
    //             ->resize($widthImageSmall, $heightImageSmall)
    //             ->save(Storage::path($fileUrlSmall));
    //     }
    //     return $result;
    // }
    
}