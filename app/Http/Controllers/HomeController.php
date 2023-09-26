<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Page;
use App\Models\Order;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use Intervention\Image\ImageManagerStatic;

use App\Mail\OrderMailable;
use Google\Service\SecurityCommandCenter\PathNodeAssociatedFinding;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller{
    public static function home(Request $request){
        /* cache HTML */
        $nameCache              = 'trang-chu.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            $language               = 'vi';
            $item                   = Page::select('*')
                                        ->whereHas('type', function($query){
                                            $query->where('code', 'home');
                                        })
                                        ->whereHas('seo', function($query){
                                            $query->where('slug', '/');
                                        })
                                        ->with('seo', 'en_seo', 'type')
                                        ->first();
            $newProducts            = Product::select('*')
                                        ->whereHas('prices.wallpapers', function(){

                                        })
                                        ->with('prices.wallpapers')
                                        ->orderBy('id', 'DESC')
                                        ->skip(0)
                                        ->take(10)
                                        ->get();
            $promotionProducts      = new \Illuminate\Database\Eloquent\Collection;
            $totalPromotionProduct  = Product::select('*')
                                        ->whereHas('prices.wallpapers', function(){

                                        })
                                        ->whereHas('prices', function($query){
                                            $query->where('sale_off', '>', 0);
                                        })
                                        ->count();
            $xhtml                  = view('wallpaper.home.index', compact('item', 'language', 'newProducts', 'promotionProducts', 'totalPromotionProduct'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function enHome(Request $request){
        /* cache HTML */
        $nameCache              = 'hone.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            $language               = 'en';
            $item                   = Page::select('*')
                                        ->whereHas('type', function($query){
                                            $query->where('code', 'home');
                                        })
                                        ->whereHas('en_seo', function($query){
                                            $query->where('slug', 'en');
                                        })
                                        ->with('seo', 'type')
                                        ->first();
            $newProducts            = Product::select('*')
                                        ->orderBy('id', 'DESC')
                                        ->skip(0)
                                        ->take(10)
                                        ->get();
            $promotionProducts      = new \Illuminate\Database\Eloquent\Collection;
            $totalPromotionProduct  = Product::select('*')
                                        ->whereHas('prices', function($query){
                                            $query->where('sale_off', '>', 0);
                                        })
                                        ->count();
            $xhtml          = view('wallpaper.home.index', compact('item', 'language', 'newProducts', 'promotionProducts', 'totalPromotionProduct'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
        return false;
    }

    public static function test(Request $request){
        // $wallpapers     = \App\Models\Wallpaper::select('*')
        //                     ->get();
        // foreach($wallpapers as $wallpaper){
        //     $filenameNotExtension   = pathinfo($wallpaper->file_url_hosting)['filename'];
        //     $extension              = pathinfo($wallpaper->file_url_hosting)['extension'];
        //     $filenameSmall          = config('image.folder_upload').$filenameNotExtension.'-small.'.$extension;
        //     $filePath               = public_path($wallpaper->file_url_hosting);
        //     if(file_exists($filePath)){
        //         $imageTmp           = ImageManagerStatic::make($filePath);
        //         $percentPixel       = $imageTmp->width()/$imageTmp->height();
        //         $widthImageNormal   = 500;
        //         $heightImageNormal  = $widthImageNormal/$percentPixel;
        //         ImageManagerStatic::make($filePath)
        //         ->encode($extension, config('image.quality'))
        //         ->resize($widthImageNormal, $heightImageNormal)
        //         ->save(Storage::path($filenameSmall));
        //     }

        // }


        // dd($wallpapers->toArray());
    }
}
