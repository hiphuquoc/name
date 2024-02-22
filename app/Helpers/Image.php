<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use App\Models\Seo;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as I;
use GuzzleHttp\Client;

class Image {

    public static function getActionImageByType($image){
        $arrayAction            = [];
        if(!empty($image)){
            $infoImage          = pathinfo($image);
            $filename           = $infoImage['filename'];
            $tmp                = explode(config('image.keyType'), $filename);
            $key                = end($tmp);
            $fullAction         = config('image.type');
            if(key_exists($key, $fullAction)){
                $arrayAction    = $fullAction[$key];
            }else {
                $arrayAction    = $fullAction['default'];
            }
        }
        return $arrayAction;
    }

    public static function streamResizedImage($imageUrl, $width = 300){
        // // Sử dụng Intervention Image để thay đổi kích thước ảnh từ URL
        // $resizedImage = I::make($imageUrl)
        //     ->resize($width, null, function ($constraint) {
        //         $constraint->aspectRatio();
        //     })
        //     ->encode(config('image.extension'));

        // // Stream ảnh về trình duyệt
        // return response($resizedImage)->header('Content-Type', 'image/jpeg');

        // Tạo một phiên làm việc mới với Guzzle
        $client = new Client();

        // Tải ảnh từ URL
        $response = $client->get(env('APP_URL').'/'.$imageUrl);

        // Đọc dữ liệu ảnh
        $imageData = $response->getBody()->getContents();

        // Xử lý ảnh với Intervention Image
        $image = I::make($imageData);

        // Kích thước mới (ví dụ: 300px chiều rộng, tự động chiều cao)
        $image->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $resizedImageData = $image->encode('data-url');

        return $resizedImageData;
    }

    public static function getUrlImageMiniByUrlImage($urlImage){
        $result     = null;
        if(!empty($urlImage)){
            /* sử dụng ảnh trong google_cloud_storage */
            $url    = config('main.google_cloud_storage.default_domain').$urlImage;
            $tmp    = pathinfo($url);
            $result = $tmp['dirname'].'/'.$tmp['filename'].'-mini.'.$tmp['extension'];
        }
        return $result;
    }

    public static function getUrlImageSmallByUrlImage($urlImage){
        $result     = null;
        if(!empty($urlImage)){
            /* sử dụng ảnh trong google_cloud_storage */
            $url    = config('main.google_cloud_storage.default_domain').$urlImage;
            $tmp    = pathinfo($url);
            $result = $tmp['dirname'].'/'.$tmp['filename'].'-small.'.$tmp['extension'];
        }
        return $result;
    }

    public static function getUrlImageLargeByUrlImage($urlImage){
        $result     = null;
        if(!empty($urlImage)){
            /* sử dụng ảnh trong google_cloud_storage */
            $url    = config('main.google_cloud_storage.default_domain').$urlImage;
            $tmp    = pathinfo($url);
            $result = $tmp['dirname'].'/'.$tmp['filename'].'-large.'.$tmp['extension'];
        }
        return $result;
    }
}