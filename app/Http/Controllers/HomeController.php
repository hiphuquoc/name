<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Page;

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
            $promotionProducts      = Product::select('*')
                                        ->whereHas('prices', function($query){
                                            $query->where('sale_off', '>', 0);
                                        })
                                        ->paginate(10);
            $newProducts            = Product::select('*')
                                        ->orderBy('id', 'DESC')
                                        ->paginate(10);
            // $hotProducts            = Product::select('*')
            //                             ->orderBy('sold', 'DESC')
            //                             ->skip(0)
            //                             ->take(8)
            //                             ->get();
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
            $xhtml          = view('wallpaper.home.index', compact('item', 'categories', 'newProducts', 'promotionProducts', 'blogs'))->render();
            /* Ghi dữ liệu - Xuất kết quả */
            if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
            echo $xhtml;
        }
    }
}
