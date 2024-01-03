<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Page;
use App\Models\Category;
use App\Models\Style;
use App\Models\Event;
use App\Http\Controllers\SettingController;

use Intervention\Image\ImageManagerStatic;

use App\Mail\OrderMailable;
use Google\Service\SecurityCommandCenter\PathNodeAssociatedFinding;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Http;
use App\Helpers\Charactor;
use App\Models\Wallpaper;

class HomeController extends Controller{
    public static function home(Request $request){
        /* cache HTML */
        $nameCache              = 'trang-chu.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            /* lưu ngôn ngữ sử dụng */
            $language           = 'vi';
            SettingController::settingLanguage($language);
            $item               = Page::select('*')
                                    ->whereHas('type', function($query){
                                        $query->where('code', 'home');
                                    })
                                    ->whereHas('seo', function($query){
                                        $query->where('slug', '/');
                                    })
                                    ->with('seo', 'en_seo', 'type')
                                    ->first();
            $products           = Product::select('product_info.*')
                                    ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                    ->whereHas('prices.wallpapers', function(){

                                    })
                                    ->with('prices.wallpapers')
                                    ->orderBy('seo.ordering', 'DESC')
                                    ->orderBy('id', 'DESC')
                                    ->get();
            $viewBy             = $request->cookie('view_by') ?? 'set';
            /* select của filter */
            $categories         = Category::all();
            $styles             = Style::all();
            $events             = Event::all();
            $xhtml              = view('wallpaper.home.index', compact('item', 'language', 'products', 'categories', 'styles', 'events', 'viewBy'))->render();
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
            /* lưu ngôn ngữ sử dụng */
            $language           = 'en';
            SettingController::settingLanguage($language);
            $item               = Page::select('*')
                                    ->whereHas('type', function($query){
                                        $query->where('code', 'home');
                                    })
                                    ->whereHas('en_seo', function($query){
                                        $query->where('slug', 'en');
                                    })
                                    ->with('seo', 'en_seo', 'type')
                                    ->first();
            $products           = Product::select('product_info.*')
                                    ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                    ->whereHas('prices.wallpapers', function(){

                                    })
                                    ->with('prices.wallpapers')
                                    ->orderBy('seo.ordering', 'DESC')
                                    ->orderBy('id', 'DESC')
                                    ->get();
            $viewBy             = $request->cookie('view_by') ?? 'set';
            $categories         = Category::all();
            $styles             = Style::all();
            $events             = Event::all();
            $xhtml              = view('wallpaper.home.index', compact('item', 'language', 'products', 'categories', 'styles', 'events', 'viewBy'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
        return false;
    }

    public static function test(Request $request){
        $wallpapers                     = \App\Models\Wallpaper::select('*')
                                            ->where('id', '<=', '478')
                                            ->get();
        foreach($wallpapers as $infoWallpaper){
            
        }

    }
}
