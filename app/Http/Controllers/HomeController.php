<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
// use App\Models\Product;
use App\Models\Page;
use App\Models\Category;
// use App\Models\Style;
use App\Models\Event;
use App\Http\Controllers\SettingController;

use Intervention\Image\ImageManagerStatic;

// use App\Mail\OrderMailable;
// use Google\Service\SecurityCommandCenter\PathNodeAssociatedFinding;
// use Illuminate\Support\Facades\Cookie;
// use Illuminate\Support\Facades\Mail;

// use Illuminate\Support\Facades\Http;
// use App\Helpers\Charactor;
// use App\Models\Wallpaper;

class HomeController extends Controller{
    public static function home(Request $request){
        /* xác định trang tiếng anh hay tiếng việt */
        $currentRoute           = Route::currentRouteName();
        /* lưu ngôn ngữ sử dụng */
        $language               = $currentRoute=='main.home' ? 'vi' : 'en';
        SettingController::settingLanguage($language);
        /* cache HTML */
        $nameCache              = $language.'home.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
        }else {
            $item               = Page::select('*')
                                    ->whereHas('type', function($query){
                                        $query->where('code', 'home');
                                    })
                                    ->when($language=='vi', function($query){
                                        $query->whereHas('seo', function($query){
                                            $query->where('slug', '/');
                                        });
                                    })
                                    ->when($language=='en', function($query){
                                        $query->whereHas('en_seo', function($query){
                                            $query->where('slug', 'en');
                                        });
                                    })
                                    ->with('seo', 'en_seo', 'type')
                                    ->first();
            /* lấy hình nền điện thoại tết */
            $slug               = 'hinh-nen-dien-thoai-tet';
            $infoCategoryTet    = Event::select('event_info.*', 'seo.slug')
                                    ->join('seo', 'seo.id', '=', 'event_info.seo_id')
                                    ->where('seo.slug', '=', $slug)
                                    // ->whereHas('products.infoProduct.prices.wallpapers', function($query){
                                    //     // Điều kiện để kiểm tra xem có ít nhất một wallpaper
                                    //     $query->whereNotNull('id');
                                    // })
                                    ->with('seo')
                                    ->with('products', function($query){
                                        $query->orderBy('id', 'DESC');
                                    })
                                    ->first();
            /* lấy hình nền điện thoại noel */
            $slug               = 'hinh-nen-dien-thoai-giang-sinh-noel';
            $infoCategoryNoel   = Event::select('event_info.*', 'seo.slug')
                                    ->join('seo', 'seo.id', '=', 'event_info.seo_id')
                                    ->where('seo.slug', '=', $slug)
                                    // ->whereHas('products.infoProduct.prices.wallpapers', function($query){
                                    //     // Điều kiện để kiểm tra xem có ít nhất một wallpaper
                                    //     $query->whereNotNull('id');
                                    // })
                                    ->with('seo')
                                    ->with('products', function($query){
                                        $query->orderBy('id', 'DESC');
                                    })
                                    ->first();
            $viewBy             = $request->cookie('view_by') ?? 'set';
            // /* select của filter */
            // $categories         = Category::all();
            // $styles             = Style::all();
            // $events             = Event::all();
            $xhtml              = view('wallpaper.home.index', compact('item', 'language', 'infoCategoryTet', 'infoCategoryNoel', 'viewBy'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
        }
        echo $xhtml;
    }

    public static function test(Request $request){
        

    }
}
