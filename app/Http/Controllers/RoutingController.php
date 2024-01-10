<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Blade;
use App\Helpers\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Style;
use App\Models\Event;
use App\Models\Page;
use App\Models\CategoryBlog;
use App\Models\Seo;


class RoutingController extends Controller{
    public function routing($slug, $slug2 = null, $slug3 = null, $slug4 = null, $slug5 = null, $slug6 = null, $slug7 = null, $slug8 = null, $slug9 = null, $slug10 = null){
        /* dùng request uri */
        $tmpSlug        = explode('/', $_SERVER['REQUEST_URI']);
        /* loại bỏ phần tử rỗng */
        $arraySlug      = [];
        foreach($tmpSlug as $slug) if(!empty($slug)&&$slug!='public') $arraySlug[] = $slug;
        /* loại bỏ hashtag và request trước khi check */
        $arraySlug[count($arraySlug)-1] = preg_replace('#([\?|\#]+).*$#imsU', '', end($arraySlug));
        $urlRequest     = implode('/', $arraySlug);
        /* check url có tồn tại? => lấy thông tin */
        $checkExists    = Url::checkUrlExists(end($arraySlug));
        /* nếu sai => redirect về link đúng */
        if(!empty($checkExists->slug_full)&&$checkExists->slug_full!=$urlRequest){
            /* ko rút gọn trên 1 dòng được => lỗi */
            return Redirect::to($checkExists->slug_full, 301);
        }
        /* nếu đúng => xuất dữ liệu */
        if(!empty($checkExists->type)){
            $flagMatch              = false;
            /* cache HTML */
            $nameCache              = self::buildNameCache($checkExists['slug_full']).'.'.config('main.cache.extension');
            $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
            $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
            if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
                $xhtml              = file_get_contents($pathCache);
                echo $xhtml;
            }else {
                /* ===== Sản phẩm ==== */
                if($checkExists->type=='product_info'){
                    $language       = $checkExists->language;
                    SettingController::settingLanguage($language);
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    $flagMatch      = true;
                    /* thông tin sản phẩm */
                    $item           = Product::select('*')
                        ->where('seo_id', $idSeo)
                        ->with('seo', 'prices.wallpapers.infoWallpaper', 'contents', 'categories')
                        ->first();
                    /* danh sách category của sản phẩm */
                    $arrayCategory  = [];
                    foreach($item->categories as $category) $arrayCategory[] = $category->infoCategory->id;
                    $keyCategory    = json_encode($arrayCategory);
                    $related        = new \Illuminate\Database\Eloquent\Collection;
                    $totalProduct   = Product::select('*')
                                        ->where('id', '!=', $item->id)
                                        ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                            $query->whereIn('id', $arrayCategory);
                                        })
                                        ->count();
                    /* sản phẩm liên quan */
                    $idSeoParent            = $item->seo->parent;
                    $infoSeoParent          = Seo::select('type')
                                                ->where('id', $idSeoParent)
                                                ->first();
                    if($infoSeoParent->type=='category_info'){
                        $tmp                = Category::select('category_info.*')
                                                ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                                ->where('seo.id', $idSeoParent)
                                                ->with('products')
                                                ->first();
                    }
                    if($infoSeoParent->type=='style_info'){
                        $tmp                = Style::select('style_info.*')
                                                ->join('seo', 'seo.id', '=', 'style_info.seo_id')
                                                ->where('seo.id', $idSeoParent)
                                                ->with('products')
                                                ->first();
                    }
                    if($infoSeoParent->type=='event_info'){
                        $tmp                = Event::select('event_info.*')
                                                ->join('seo', 'seo.id', '=', 'event_info.seo_id')
                                                ->where('seo.id', $idSeoParent)
                                                ->with('products.infoProduct.seo')
                                                ->first();
                    }
                    $related            = $tmp->products;
                    /* breadcrumb */
                    $breadcrumb         = Url::buildBreadcrumb($checkExists->slug_full, $language);
                    $xhtml              = view('wallpaper.product.index', compact('item', 'breadcrumb', 'related', 'totalProduct', 'keyCategory', 'language'))->render();
                }

                /* ===== Các trang chủ đề/phong cách/sự kiện ==== */
                if($checkExists->type=='category_info'||$checkExists->type=='style_info'||$checkExists->type=='event_info'){
                    $language       = $checkExists->language;
                    SettingController::settingLanguage($language);
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    $flagMatch      = true;
                    $viewBy         = request()->cookie('view_by') ?? 'set';
                    /* breadcrumb */
                    $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full, $language);
                    /* key_search */
                    $keySearch      = request('search') ?? null;
                    /* ===== Chủ để ===== */
                    if($checkExists->type=='category_info'){
                        /* thư mục chứa content */
                        $folderContent  = $language=='vi' ? config('main.storage.contentCategory') : config('main.storage.enContentCategory');
                        /* thông tin category */
                        $item           = Category::select('*')
                                            ->where('seo_id', $idSeo)
                                            ->with('seo', 'en_seo')
                                            ->first();
                        /* danh sách product => lấy riêng để dễ truyền vào template */
                        $arrayCategory  = Category::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
                        $products       = Product::select('product_info.*')
                                            ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                            ->when(!empty($keySearch), function($query) use($keySearch){
                                                $query->where('code', 'like', '%'.$keySearch.'%')
                                                    ->orWhere('name', 'like', '%'.$keySearch.'%')
                                                    ->orWhere('en_name', 'like', '%'.$keySearch.'%');
                                            })
                                            ->whereHas('prices.wallpapers', function($query){

                                            })
                                            ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                                $query->whereIn('id', $arrayCategory);
                                            })
                                            ->with('seo', 'en_seo', 'prices')
                                            ->orderBy('seo.ordering', 'DESC')
                                            ->orderBy('id', 'DESC')
                                            ->get();
                        $categoryChoose = $item;
                    }
                    /* ===== Phong cách ===== */
                    if($checkExists->type=='style_info'){
                        /* thư mục chứa content */
                        $folderContent  = $language=='vi' ? config('main.storage.contentStyle') : config('main.storage.enContentStyle');
                        /* thông tin category */
                        $item           = Style::select('*')
                                            ->where('seo_id', $idSeo)
                                            ->with('seo', 'en_seo')
                                            ->first();
                        $idStyle        = $item->id ?? 0;
                        /* danh sách product */
                        $products       = Product::select('product_info.*')
                                            ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                            ->whereHas('prices.wallpapers', function($query){

                                            })
                                            ->whereHas('styles.infoStyle', function($query) use($idStyle){
                                                $query->where('id', $idStyle);
                                            })
                                            ->with('seo', 'en_seo', 'prices')
                                            ->orderBy('seo.ordering', 'DESC')
                                            ->orderBy('id', 'DESC')
                                            ->get();
                        $styleChoose    = $item;
                    }
                    /* ===== Sự kiện ===== */
                    if($checkExists->type=='event_info'){
                        /* thư mục chứa content */
                        $folderContent  = $language=='vi' ? config('main.storage.contentEvent') : config('main.storage.enContentEvent');
                        /* thông tin category */
                        $item           = Event::select('*')
                                            ->where('seo_id', $idSeo)
                                            ->with('seo', 'en_seo')
                                            ->first();
                        $idEvent        = $item->id ?? 0;
                        /* danh sách product */
                        $products       = Product::select('product_info.*')
                                            ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                            ->whereHas('prices.wallpapers', function($query){

                                            })
                                            ->whereHas('events.infoEvent', function($query) use($idEvent){
                                                $query->where('id', $idEvent);
                                            })
                                            ->with('seo', 'en_seo', 'prices')
                                            ->orderBy('seo.ordering', 'DESC')
                                            ->orderBy('id', 'DESC')
                                            ->get();
                        $eventChoose    = $item;
                    }
                    /* content */
                    $filenameContent    = $language=='vi' ? $folderContent.$item->seo->slug.'.blade.php' : $folderContent.$item->en_seo->slug.'.blade.php';
                    $content            = Blade::render(Storage::get($filenameContent));
                    /* select của filter */
                    $categories         = Category::all();
                    $styles             = Style::all();
                    $events             = Event::all();
                    /* giá trị selectBox */
                    if(empty($categoryChoose)) $categoryChoose = [];
                    if(!empty(request('category_info_id'))){
                        $categoryChoose = Category::select('*')
                                            ->where('id', request('category_info_id'))
                                            ->with('seo', 'en_seo')
                                            ->first();
                    }
                    if(empty($styleChoose)) $styleChoose = [];
                    if(!empty(request('style_info_id'))){
                        $styleChoose    = Style::select('*')
                                            ->where('id', request('style_info_id'))
                                            ->with('seo', 'en_seo')
                                            ->first();
                    }
                    if(empty($eventChoose)) $eventChoose = [];
                    if(!empty(request('event_info_id'))){
                        $eventChoose    = Event::select('*')
                                            ->where('id', request('event_info_id'))
                                            ->with('seo', 'en_seo')
                                            ->first();
                    }
                    /* lấy giao diện */
                    $xhtml              = view('wallpaper.category.index', compact('item', 'products', 'categories', 'styles', 'events', 'breadcrumb', 'content', 'language', 'viewBy', 'categoryChoose', 'styleChoose', 'eventChoose'))->render();
                }
                /* ===== Trang ==== */
                if($checkExists->type=='page_info'){
                    $flagMatch      = true;
                    $language       = $checkExists->language;
                    SettingController::settingLanguage($language);
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    /* thông tin brand */
                    $item           = Page::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->with('seo', 'files')
                                        ->first();
                    /* breadcrumb */
                    $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full);
                    /* content */
                    if($language=='en'){
                        $content        = Blade::render(Storage::get(config('main.storage.enContentPage').$item->en_seo->slug.'.blade.php'));
                    }else {
                        $content        = Blade::render(Storage::get(config('main.storage.contentPage').$item->seo->slug.'.blade.php'));
                    }
                    /* page related */
                    $typePages      = Page::select('page_info.*')
                                        ->where('show_sidebar', 1)
                                        ->join('seo', 'seo.id', '=', 'page_info.seo_id')
                                        ->with('type')
                                        ->orderBy('seo.ordering', 'DESC')
                                        ->get()
                                        ->groupBy('type.id');
                    $xhtml          = view('wallpaper.page.index', compact('item', 'language', 'breadcrumb', 'content', 'typePages'))->render();
                }
                /* Ghi dữ liệu - Xuất kết quả */
                if($flagMatch==true){
                    if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
                    echo $xhtml;
                }else {
                    return \App\Http\Controllers\ErrorController::error404();
                }
            }
            return false;
        }else {
            return \App\Http\Controllers\ErrorController::error404();
        }
    }

    public static function buildNameCache($slugFull){
        $result     = null;
        if(!empty($slugFull)){
            $tmp    = explode('/', $slugFull);
            $result = [];
            foreach($tmp as $t){
                if(!empty($t)) $result[] = $t;
            }
            $result = implode('-', $result);
        }
        return $result;
    }

    private static function convertItemViToEn($item){
        $item           = $item->en_seo;
        $item->seo      = $item->seo->infoEnSeo;
        return $item;
    }
}
