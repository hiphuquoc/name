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
        $checkExists    = Url::checkUrlExists(end($arraySlug));
        /* nếu sai => redirect về link đúng */
        if(!empty($checkExists->slug_full)&&$checkExists->slug_full!=$urlRequest){
            /* ko rút gọn trên 1 dòng được => lỗi */
            return Redirect::to($checkExists->slug_full, 301);
        }
        /* ============== nếu đúng => xuất dữ liệu */
        if(!empty($checkExists->type)){
            /* ngôn ngữ */
            $language                   = $checkExists->language;
            SettingController::settingLanguage($language);
            /* chế đệ xem */
            $viewBy     = Cookie::get('view_by') ?? 'set';
            $flagMatch              = false;
            /* cache HTML */
            $nameCache              = self::buildNameCache($checkExists['slug_full'], [$viewBy]).'.'.config('main.cache.extension');
            $pathCache              = Storage::path(config('main.cache.folderSave')).$nameCache;
            $cacheTime    	        = env('APP_CACHE_TIME') ?? 1800;
            if(file_exists($pathCache)&&$cacheTime>(time() - filectime($pathCache))){
                $xhtml              = file_get_contents($pathCache);
                echo $xhtml;
            }else {
                /* ===== từng ảnh ===== */
                if($checkExists->type=='free_wallpaper_info'){
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    $flagMatch      = true;
                    /* thông tin wallpaper */
                    $item           = FreeWallpaper::select('*')
                        ->where('seo_id', $idSeo)
                        ->with('seo', 'en_seo', 'tags', 'contents', 'categories')
                        ->first();
                    $idNot          = $item->id;
                    /* danh sách category của sản phẩm */
                    $arrayIdCategory  = [];
                    foreach($item->categories as $category) $arrayIdCategory[] = $category->infoCategory->id;
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
                    /* breadcrumb */
                    $breadcrumb         = Url::buildBreadcrumb($checkExists->slug_full, $language);
                    $xhtml              = view('wallpaper.freeWallpaper.index', compact('item', 'idNot', 'breadcrumb', 'total', 'loaded', 'related', 'language', 'arrayIdCategory'))->render();
                }
                /* ===== Tag ==== */
                if($checkExists->type=='tag_info'){
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    $flagMatch      = true;
                    /* breadcrumb */
                    $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full, $language);
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    $item           = Tag::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->with('seo', 'en_seo', 'freeWallpapers')
                                        ->first();
                    /* content */
                    if($language=='en'){
                        $content        = Blade::render(Storage::get(config('main.storage.enContentTag').$item->en_seo->slug.'.blade.php'));
                    }else {
                        $content        = Blade::render(Storage::get(config('main.storage.contentTag').$item->seo->slug.'.blade.php'));
                    }
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
                    $xhtml              = view('wallpaper.tag.index', compact('item', 'breadcrumb', 'content', 'wallpapers', 'total', 'arrayIdCategory', 'loaded', 'language', 'user'))->render();
                }
                /* ===== Sản phẩm ==== */
                if($checkExists->type=='product_info'){
                    $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                    $flagMatch      = true;
                    /* thông tin sản phẩm */
                    $item           = Product::select('*')
                        ->where('seo_id', $idSeo)
                        ->with('seo', 'prices.wallpapers.infoWallpaper', 'contents', 'categories')
                        ->first();
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
                    $infoSeoParent      = Seo::select('type')
                                            ->where('id', $idSeoParent)
                                            ->first();
                    $tmp                = Category::select('category_info.*')
                                            ->join('seo', 'seo.id', '=', 'category_info.seo_id')
                                            ->where('seo.id', $idSeoParent)
                                            ->with('products')
                                            ->first();
                    $related            = $tmp->products;
                    /* breadcrumb */
                    $breadcrumb         = Url::buildBreadcrumb($checkExists->slug_full, $language);
                    $xhtml              = view('wallpaper.product.index', compact('item', 'breadcrumb', 'total', 'arrayIdCategory', 'language'))->render();
                }
                /* ===== Các trang chủ đề/phong cách/sự kiện ==== */
                foreach(config('main.category_type') as $type){
                    if($checkExists->type==$type['key']){
                        $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                        $flagMatch      = true;
                        /* breadcrumb */
                        $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full, $language);
                        $idSeo          = $language=='vi' ? $checkExists->id : $checkExists->seo->infoSeo->id;
                        $item           = Category::select('*')
                                            ->where('seo_id', $idSeo)
                                            ->with('seo', 'en_seo')
                                            ->first();
                        /* content */
                        if($language=='en'){
                            $content        = Blade::render(Storage::get(config('main.storage.enContentCategory').$item->en_seo->slug.'.blade.php'));
                        }else {
                            $content        = Blade::render(Storage::get(config('main.storage.contentCategory').$item->seo->slug.'.blade.php'));
                        }
                        /* ===== miễn phí */
                        $flagFree       = false;
                        if($checkExists->slug=='hinh-nen-dien-thoai-mien-phi'||$checkExists->slug=='free-phone-wallpapers'){
                            $flagFree   = true;
                            /* tìm kiếm bằng hình ảnh */
                            $idFreeWallpaper    = $request->get('idFreeWallpaper') ?? 0;
                            $infoFreeWallpaper  = FreeWallpaper::select('*')
                                                    ->where('id', $idFreeWallpaper)
                                                    ->first();
                            if(!empty($infoFreeWallpaper)){
                                /* trường hợp search bằng hình ảnh ::: */
                                $arrayIdCategory    = [];
                                foreach($infoFreeWallpaper->categories as $category){
                                    $arrayIdCategory[] = $category->infoCategory->id;
                                }
                                $typeWhere          = 'and';
                            }else {
                                /* trường hợp bình thường không phải search ::: lấy danh sách category hiện tại và con (nếu có) */
                                $tmp                = Category::getTreeCategoryByInfoCategory($item, []);
                                $arrayIdCategory = [$item->id];
                                foreach($tmp as $t) $arrayIdCategory[] = $t->id;
                                $typeWhere          = 'or';
                            }
                            /* tìm kiếm bằng feeling */
                            $searchFeeling = $request->get('search_feeling') ?? [];
                            foreach($searchFeeling as $feeling){
                                if($feeling=='all'){ /* trường hợp tìm kiếm có all thì clear */
                                    $searchFeeling = [];
                                    break;
                                }
                            }
                            /* lấy wallpapers */
                            $loaded         = 10;
                            $sortBy         = Cookie::get('sort_by') ?? null;
                            $filters        = $request->get('filters') ?? [];
                            $user           = Auth::user();
                            $wallpapers     = FreeWallpaper::select('*')
                                                ->whereHas('categories', function($query) use($arrayIdCategory, $typeWhere) {
                                                    if(!empty($arrayIdCategory)){
                                                        if ($typeWhere == 'or') {
                                                            $query->whereIn('category_info_id', $arrayIdCategory);
                                                        } elseif ($typeWhere == 'and') {
                                                            $query->where(function($subquery) use($arrayIdCategory) {
                                                                foreach($arrayIdCategory as $c) {
                                                                    $subquery->where('category_info_id', $c);
                                                                }
                                                            });
                                                        }
                                                    }
                                                })
                                                ->when(!empty($filters), function($query) use($filters){
                                                    foreach($filters as $filter){
                                                        $query->whereHas('categories.infoCategory', function($query) use($filter){
                                                            $query->where('id', $filter);
                                                        });
                                                    }
                                                })
                                                ->when(!empty($searchFeeling), function($query) use ($searchFeeling) {
                                                    $query->whereHas('feeling', function($subquery) use ($searchFeeling) {
                                                        $subquery->whereIn('type', $searchFeeling);
                                                    });
                                                })
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
                                                ->whereHas('categories', function($query) use($arrayIdCategory, $typeWhere) {
                                                    if(!empty($arrayIdCategory)){
                                                        if ($typeWhere == 'or') {
                                                            $query->whereIn('category_info_id', $arrayIdCategory);
                                                        } elseif ($typeWhere == 'and') {
                                                            $query->where(function($subquery) use($arrayIdCategory) {
                                                                foreach($arrayIdCategory as $c) {
                                                                    $subquery->where('category_info_id', $c);
                                                                }
                                                            });
                                                        }
                                                    }
                                                })
                                                ->when(!empty($filters), function($query) use($filters){
                                                    foreach($filters as $filter){
                                                        $query->whereHas('categories.infoCategory', function($query) use($filter){
                                                            $query->where('id', $filter);
                                                        });
                                                    }
                                                })
                                                ->when(!empty($searchFeeling), function($query) use ($searchFeeling) {
                                                    $query->whereHas('feeling', function($subquery) use ($searchFeeling) {
                                                        $subquery->whereIn('type', $searchFeeling);
                                                    });
                                                })
                                                ->count();
                            $xhtml              = view('wallpaper.category.index', compact('item', 'breadcrumb', 'content', 'wallpapers', 'arrayIdCategory', 'total', 'loaded', 'language', 'infoFreeWallpaper', 'typeWhere', 'user', 'searchFeeling'))->render();
                        }
                        /* ===== trả phí */
                        if($flagFree==false){
                            /* key_search */
                            $keySearch      = request('search') ?? null;
                            /* array category */
                            $tmp            = Category::getTreeCategoryByInfoCategory($item, []);
                            $arrayIdCategory = [$item->id];
                            foreach($tmp as $t) $arrayIdCategory[] = $t->id;
                            /* thư mục chứa content */
                            $folderContent  = $language=='vi' ? config('main.storage.contentCategory') : config('main.storage.enContentCategory');
                            /* thông tin category */
                            $item           = Category::select('*')
                                                ->where('seo_id', $idSeo)
                                                ->with('seo', 'en_seo')
                                                ->first();
                            /* chế độ xem */
                            $viewBy             = request()->cookie('view_by') ?? 'set';
                            /* filter nếu có */
                            $filters        = $request->get('filters') ?? [];
                            /* danh sách product => lấy riêng để dễ truyền vào template */
                            $loaded         = 10;
                            $sortBy         = Cookie::get('sort_by') ?? null;
                            $arrayIdCategory  = Category::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
                            $wallpapers     = Product::select('product_info.*')
                                                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                                ->when(!empty($keySearch), function($query) use($keySearch){
                                                    $query->where('code', 'like', '%'.$keySearch.'%')
                                                        ->orWhere('name', 'like', '%'.$keySearch.'%')
                                                        ->orWhere('en_name', 'like', '%'.$keySearch.'%');
                                                })
                                                ->when(!empty($filters), function($query) use($filters){
                                                    foreach($filters as $filter){
                                                        $query->whereHas('categories.infoCategory', function($query) use($filter){
                                                            $query->where('id', $filter);
                                                        });
                                                    }
                                                })
                                                ->whereHas('prices.wallpapers', function($query){

                                                })
                                                ->whereHas('categories.infoCategory', function($query) use($arrayIdCategory){
                                                    $query->whereIn('id', $arrayIdCategory);
                                                })
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
                                                ->with('seo', 'en_seo', 'prices')
                                                ->orderBy('seo.ordering', 'DESC')
                                                ->orderBy('id', 'DESC')
                                                ->skip(0)
                                                ->take($loaded)
                                                ->get();
                            $total          = Product::select('product_info.*')
                                                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                                ->when(!empty($keySearch), function($query) use($keySearch){
                                                    $query->where('code', 'like', '%'.$keySearch.'%')
                                                        ->orWhere('name', 'like', '%'.$keySearch.'%')
                                                        ->orWhere('en_name', 'like', '%'.$keySearch.'%');
                                                })
                                                ->when(!empty($filters), function($query) use($filters){
                                                    foreach($filters as $filter){
                                                        $query->whereHas('categories.infoCategory', function($query) use($filter){
                                                            $query->where('id', $filter);
                                                        });
                                                    }
                                                })
                                                ->whereHas('prices.wallpapers', function($query){

                                                })
                                                ->whereHas('categories.infoCategory', function($query) use($arrayIdCategory){
                                                    $query->whereIn('id', $arrayIdCategory);
                                                })
                                                ->count();
                            /* category Choose */
                            $categoryChoose     = $item;
                            /* content */
                            $filenameContent    = $language=='vi' ? $folderContent.$item->seo->slug.'.blade.php' : $folderContent.$item->en_seo->slug.'.blade.php';
                            $content            = Blade::render(Storage::get($filenameContent));
                            $xhtml              = view('wallpaper.categoryMoney.index', compact('item', 'breadcrumb', 'content', 'wallpapers', 'arrayIdCategory', 'total', 'loaded', 'language', 'viewBy'))->render();
                        }
                    }
                }
                /* ===== Trang ==== */
                if($checkExists->type=='page_info'){
                    $flagMatch      = true;
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
