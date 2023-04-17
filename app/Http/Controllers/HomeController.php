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

use App\Mail\OrderMailable;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller{
    public static function home(Request $request){
        /* cache HTML */
        $nameCache              = 'trang-chu.'.config('main.cache.extension');
        $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
        $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
        if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
            $xhtml              = file_get_contents($pathCache);
            echo $xhtml;
        }else {
            $item                   = Page::select('*')
                                        ->whereHas('type', function($query){
                                            $query->where('code', 'home');
                                        })
                                        ->whereHas('seo', function($query){
                                            $query->where('slug', '/');
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
            $categories             = Category::select('*')
                                        ->whereHas('seo', function($query){
                                            $query->where('level', 1);
                                        })
                                        ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                        ->orderBy('seo.ordering', 'DESC')
                                        ->get();
            $blogs                  = Blog::select('*')
                                        ->whereHas('categories.infoCategory.seo', function($query){
                                            $query->where('slug', 'blog-lam-dep');
                                        })
                                        ->with('seo')
                                        ->get();
            $xhtml          = view('wallpaper.home.index', compact('item', 'categories', 'newProducts', 'promotionProducts', 'totalPromotionProduct', 'blogs'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
            echo $xhtml;
        }
    }

    public static function test(Request $request){
        $orderInfo          = Order::select('*')
                                ->where('email', '!=', '')
                                ->where('payment_status', 1)
                                ->orderBy('id', 'DESC')
                                ->first();
        // Mail::to('wallpaperdienthoai@gmail.com')->send(new OrderMailable($orderInfo));

        return view('wallpaper.email.order', ['order' => $orderInfo]);
        
    }
}
