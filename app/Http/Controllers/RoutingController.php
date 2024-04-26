<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Blade;
use App\Helpers\Url;
use App\Http\Controllers\CategoryMoneyController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Style;
use App\Models\Event;
use App\Models\Page;
use App\Models\CategoryBlog;
use App\Models\FreeWallpaper;
use App\Models\Seo;
use Illuminate\Support\Facades\Auth;


class RoutingController extends Controller{
    public function routing(Request $request, $slug, $slug2 = null, $slug3 = null, $slug4 = null, $slug5 = null, $slug6 = null, $slug7 = null, $slug8 = null, $slug9 = null, $slug10 = null){
        /* dùng request uri */
        $tmpSlug        = explode('/', $_SERVER['REQUEST_URI']);
        /* loại bỏ phần tử rỗng */
        $arraySlug      = [];
        foreach($tmpSlug as $slug) if(!empty($slug)&&$slug!='public') $arraySlug[] = $slug;
        /* loại bỏ hashtag và request trước khi check */
        $arraySlug[count($arraySlug)-1] = preg_replace('#([\?|\#]+).*$#imsU', '', end($arraySlug));
        $urlRequest     = implode('/', $arraySlug);
        /* check url có tồn tại? => lấy thông tin */
        $itemSeo    = Url::checkUrlExists(end($arraySlug));
        /* nếu sai => redirect về link đúng */
        if(!empty($itemSeo->slug_full)&&$itemSeo->slug_full!=$urlRequest){
            /* ko rút gọn trên 1 dòng được => lỗi */
            return Redirect::to($itemSeo->slug_full, 301);
        }
        /* ============== nếu đúng => xuất dữ liệu */
        if(!empty($itemSeo->type)){
            /* ngôn ngữ */
            $language               = $itemSeo->language;
            SettingController::settingLanguage($language);
            /* chế đệ xem */
            $flagMatch              = false;
            /* cache HTML */
            $nameCache              = self::buildNameCache($itemSeo['slug_full']).'.'.config('main.cache.extension');
            $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
            $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
            if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
                $xhtml              = file_get_contents($pathCache);
                echo $xhtml;
            }else {
                /* breadcrumb */
                $breadcrumb     = Url::buildBreadcrumb($itemSeo->slug_full);
                /* thông tin */
                $tableName      = $itemSeo->type;
                $modelName      = config('tablemysql.'.$itemSeo->type.'.model_name');
                $modelInstance  = resolve("\App\Models\\$modelName");
                $idSeo          = $itemSeo->id;
                $item           = $modelInstance::select('*')
                                    ->whereHas('seos', function($query) use($idSeo){
                                        $query->where('seo_id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                /* ===== từng ảnh ===== */
                if($itemSeo->type=='free_wallpaper_info'){
                    $flagMatch      = true;
                    $idNot          = $item->id;
                    /* danh sách category của sản phẩm */
                    $arrayIdCategory  = [];
                    foreach($item->categories as $category) {
                        if(!empty($category->infoCategory)) {
                            $arrayIdCategory[] = $category->infoCategory->id;
                        }
                    }
                    $total          = FreeWallpaper::select('*')
                                        ->where('id', '!=', $item->id)
                                        ->whereHas('categories.infoCategory', function($query) use($arrayIdCategory){
                                            $query->whereIn('id', $arrayIdCategory);
                                        })
                                        ->count();
                    $loaded         = 0;
                    /* sản phẩm liên quan */
                    $related            = FreeWallpaper::select('*')
                                            ->where('id', '!=', $item->id)
                                            ->whereHas('categories.infoCategory', function($query) use($arrayIdCategory){
                                                $query->whereIn('id', $arrayIdCategory);
                                            })
                                            ->orderBy('id', 'DESC')
                                            ->skip(0)
                                            ->take($loaded)
                                            ->get();
                    $xhtml              = view('wallpaper.freeWallpaper.index', compact('item', 'itemSeo', 'idNot', 'breadcrumb', 'total', 'loaded', 'related', 'language', 'arrayIdCategory'))->render();
                }
                /* ===== Tag ==== */
                if($itemSeo->type=='tag_info'){
                    $flagMatch      = true;
                    // /* tìm kiếm bằng feeling */
                    // $searchFeeling = $request->get('search_feeling') ?? [];
                    // foreach($searchFeeling as $feeling){
                    //     if($feeling=='all'){ /* trường hợp tìm kiếm có all thì clear */
                    //         $searchFeeling = [];
                    //         break;
                    //     }
                    // }
                    /* lấy wallpapers */
                    $loaded         = 10;
                    $sortBy         = Cookie::get('sort_by') ?? null;
                    $filters        = $request->get('filters') ?? [];
                    $user           = Auth::user();
                    $arrayIdTag     = [$item->id];
                    $arrayIdCategory    = [];
                    $wallpapers     = FreeWallpaper::select('*')
                                        ->when(!empty($arrayIdTag), function($query) use($arrayIdTag){
                                            $query->whereHas('tags', function($subquery) use($arrayIdTag){
                                                $subquery->whereIn('tag_info_id', $arrayIdTag);
                                            });
                                        })
                                        ->when(!empty($filters), function($query) use($filters){
                                            foreach($filters as $filter){
                                                $query->whereHas('categories.infoCategory', function($query) use($filter){
                                                    $query->where('id', $filter);
                                                });
                                            }
                                        })
                                        // ->when(!empty($searchFeeling), function($query) use ($searchFeeling) {
                                        //     $query->whereHas('feeling', function($subquery) use ($searchFeeling) {
                                        //         $subquery->whereIn('type', $searchFeeling);
                                        //     });
                                        // })
                                        ->when(empty($sortBy), function($query){
                                            $query->orderBy('id', 'DESC');
                                        })
                                        ->when($sortBy=='new'||$sortBy=='propose', function($query){
                                            $query->orderBy('id', 'DESC');
                                        })
                                        ->when($sortBy=='favourite', function($query){
                                            $query->orderBy('heart', 'DESC')
                                                    ->orderBy('id', 'DESC');
                                        })
                                        ->when($sortBy=='old', function($query){
                                            $query->orderBy('id', 'ASC');
                                        })
                                        ->skip(0)
                                        ->take($loaded)
                                        ->get();
                    $total          = FreeWallpaper::select('*')
                                        ->when(!empty($arrayIdTag), function($query) use($arrayIdTag){
                                            $query->whereHas('tags', function($subquery) use($arrayIdTag){
                                                $subquery->whereIn('tag_info_id', $arrayIdTag);
                                            });
                                        })
                                        ->when(!empty($filters), function($query) use($filters){
                                            foreach($filters as $filter){
                                                $query->whereHas('categories.infoCategory', function($query) use($filter){
                                                    $query->where('id', $filter);
                                                });
                                            }
                                        })
                                        // ->when(!empty($searchFeeling), function($query) use ($searchFeeling) {
                                        //     $query->whereHas('feeling', function($subquery) use ($searchFeeling) {
                                        //         $subquery->whereIn('type', $searchFeeling);
                                        //     });
                                        // })
                                        ->count();
                    $xhtml              = view('wallpaper.tag.index', compact('item', 'itemSeo', 'breadcrumb', 'wallpapers', 'total', 'arrayIdCategory', 'loaded', 'language', 'user'))->render();
                }
                /* ===== Sản phẩm ==== */
                if($itemSeo->type=='product_info'){
                    $flagMatch      = true;
                    /* danh sách category của sản phẩm */
                    $arrayIdCategory  = [];
                    foreach($item->categories as $category) $arrayIdCategory[] = $category->infoCategory->id;
                    $total          = Product::select('*')
                                        ->where('id', '!=', $item->id)
                                        ->whereHas('categories.infoCategory', function($query) use($arrayIdCategory){
                                            $query->whereIn('id', $arrayIdCategory);
                                        })
                                        ->count();
                    $loaded         = 0;
                    /* sản phẩm liên quan */
                    $idSeoParent        = $item->seo->parent;
                    $itemSeoParent      = Seo::select('type')
                                            ->where('id', $idSeoParent)
                                            ->first();
                    $tmp                = Category::select('category_info.*')
                                            ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                            ->where('seo.id', $idSeoParent)
                                            ->with('products')
                                            ->first();
                    $related            = $tmp->products;
                    $xhtml              = view('wallpaper.product.index', compact('item', 'itemSeo', 'breadcrumb', 'total', 'arrayIdCategory', 'language'))->render();
                }
                /* ===== Các trang chủ đề/phong cách/sự kiện ==== */
                foreach(config('main.category_type') as $type){
                    if($itemSeo->type==$type['key']){
                        $flagMatch      = true;
                        /* ===== miễn phí */
                        $flagFree       = false;
                        if(in_array($itemSeo->slug, config('main.url_free_wallpaper_category'))){
                            $flagFree   = true;
                            $params     = [];
                            /* tìm kiếm bằng feeling */
                            $searchFeeling = $request->get('search_feeling') ?? [];
                            foreach($searchFeeling as $feeling){
                                if($feeling=='all'){ /* trường hợp tìm kiếm có all thì clear */
                                    $searchFeeling = [];
                                    break;
                                }
                            }
                            /* lấy wallpapers */
                            $tmp                                = Category::getTreeCategoryByInfoCategory($item, []);
                            $arrayIdCategory                    = [$item->id];
                            foreach($tmp as $t) $arrayIdCategory[] = $t->id;
                            $params['array_category_info_id']   = $arrayIdCategory;
                            $params['loaded']                   = 0;
                            $params['request_load']             = 50; /* lấy 50 để khai báo schema */
                            $params['sort_by']                  = Cookie::get('sort_by') ?? null;
                            $params['filters']                  = $request->get('filters') ?? [];
                            $tmp                                = CategoryController::getFreeWallpapers($params);
                            $wallpapers                         = $tmp['wallpapers'];
                            $total                              = $tmp['total'];
                            $loaded                             = $tmp['loaded'];
                            $user                               = Auth::user();
                            $xhtml              = view('wallpaper.category.index', compact('item', 'itemSeo', 'breadcrumb', 'wallpapers', 'arrayIdCategory', 'total', 'loaded', 'language', 'user', 'searchFeeling'))->render();
                        }
                        /* ===== trả phí */
                        if($flagFree==false){
                            $params         = [];
                            /* key_search */
                            $params['key_search'] = request('search') ?? null;
                            $arrayIdCategory  = Category::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
                            // dd($request->all());
                            $params['array_category_info_id'] = $arrayIdCategory;
                            /* chế độ xem */
                            $viewBy             = request()->cookie('view_by') ?? 'each_set';
                            /* filter nếu có */
                            $params['filters']  = $request->get('filters') ?? [];
                            /* danh sách product => lấy riêng để dễ truyền vào template */
                            $params['loaded']   = 0;
                            $params['request_load'] = 10;
                            $params['sort_by']  = Cookie::get('sort_by') ?? null;
                            /* lấy thông tin dựa vào params */
                            $response           = CategoryMoneyController::getWallpapers($params, $language);
                            $wallpapers         = $response['wallpapers'];
                            $total              = $response['total'];
                            $loaded             = $response['loaded'];
                            $xhtml              = view('wallpaper.categoryMoney.index', compact('item', 'itemSeo', 'breadcrumb', 'wallpapers', 'arrayIdCategory', 'total', 'loaded', 'language', 'viewBy'))->render();
                        }
                    }
                }
                /* ===== Trang ==== */
                if($itemSeo->type=='page_info'){
                    $flagMatch      = true;
                    /* page related */
                    $item           = Page::select('*')
                                        ->whereHas('seos.infoSeo', function($query) use($idSeo){
                                            $query->where('id', $idSeo);
                                        })
                                        ->with('type')
                                        ->first();
                    $xhtml  = view('wallpaper.page.index', compact('item', 'itemSeo', 'language', 'breadcrumb'))->render();
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

    public static function buildNameCache($slugFull, $prefix = []){
        $result     = $prefix;
        if(!empty($slugFull)){
            $tmp    = explode('/', $slugFull);
            foreach($tmp as $t){
                if(!empty($t)) $result[] = $t;
            }
            $result = implode('-', $result);
        }
        return $result;
    }
}
