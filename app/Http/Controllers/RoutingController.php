<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Blade;
use App\Helpers\Url;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Page;
use App\Models\CategoryBlog;
use App\Models\Blog;


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
                switch($checkExists->type){
                    case 'product_info':
                        $flagMatch      = true;
                        /* thông tin sản phẩm */
                        $item           = Product::select('*')
                            ->where('seo_id', $checkExists->id)
                            ->with('seo', 'prices', 'contents', 'categories')
                            ->first();
                        /* danh sách category của sản phẩm */
                        $arrayCategory  = [];
                        foreach($item->categories as $category) $arrayCategory[] = $category->infoCategory->id;
                        $related        = Product::select('*')
                                            ->where('id', '!=', $item->id)
                                            ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                                $query->whereIn('id', $arrayCategory);
                                            })
                                            ->with('seo', 'prices.files')
                                            ->get();
                        /* breadcrumb */
                        $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full);
                        $xhtml          = view('wallpaper.product.index', compact('item', 'breadcrumb', 'related'))->render();
                        break;
                    case 'category_info':
                        $flagMatch      = true;
                        /* thông tin category */
                        $item           = Category::select('*')
                                            ->where('seo_id', $checkExists->id)
                                            ->with('seo', 'files', 'categoryBlogs.infoCategoryBlog.blogs.infoBlog.seo')
                                            ->first();
                        /* danh sách product => lấy riêng để dễ truyền vào template */
                        $arrayCategory  = Category::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
                        $products       = Product::select('*')
                                            ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                                $query->whereIn('id', $arrayCategory);
                                            })
                                            ->with('seo', 'prices')
                                            ->orderBy('id', 'DESC')
                                            ->get();
                        /* lấy thông tin category dưới 1 cấp => gộp vào collection */
                        $idSeo          = $item->seo->id;
                        $categories     = Category::select('*')
                                            ->whereHas('seo', function($query) use($idSeo){
                                                $query->where('parent', $idSeo);
                                            })
                                            ->get();
                        /* lấy thông tin nghành hàng của tất cả sản phẩm trong category */
                        $brands             = new \Illuminate\Database\Eloquent\Collection;
                        foreach($products as $product){
                            if (!$brands->contains('id', $product->brand->id)){
                                $brands[]   = $product->brand;
                            }
                        }
                        /* content */
                        $content            = Blade::render(Storage::get(config('main.storage.contentCategory').$item->seo->slug.'.blade.php'));
                        /* breadcrumb */
                        $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full);
                        $xhtml          = view('wallpaper.category.index', compact('item', 'products', 'breadcrumb', 'brands', 'categories', 'content'))->render();
                        break;
                    // case 'brand_info':
                    //     $flagMatch      = true;
                    //     /* thông tin brand */
                    //     $item           = Brand::select('*')
                    //                         ->where('seo_id', $checkExists->id)
                    //                         ->with('seo', 'files')
                    //                         ->first();
                    //     /* danh sách product => lấy riêng để dễ truyền vào template */
                    //     $idBrand        = $item->id ?? 0;
                    //     $products       = Product::select('*')
                    //                         ->whereHas('brand', function($query) use($idBrand){
                    //                             $query->where('brand_id', $idBrand);
                    //                         })
                    //                         ->with('seo', 'files', 'prices', 'contents', 'categories', 'brand')
                    //                         ->get();
                    //     /* danh sách tất cả category của những sản phẩm trên */
                    //     $categories     = new \Illuminate\Database\Eloquent\Collection;
                    //     foreach($products as $product){
                    //         foreach($product->categories as $category){
                    //             if (!$categories->contains('id', $category->infoCategory->id)){
                    //                 $categories[]   = $category->infoCategory;
                    //             }
                    //         }
                    //     }
                    //     /* lấy thêm brands cho filter => duy nhất */
                    //     $brands         = new \Illuminate\Database\Eloquent\Collection;
                    //     $brands[]       = $item;  
                    //     /* lấy thông tin category của các sản phẩm */
                    //     $idSeo          = $item->seo->id;
                    //     $item->childs   = Category::select('*')
                    //                         ->whereHas('seo', function($query) use($idSeo){
                    //                             $query->where('parent', $idSeo);
                    //                         })
                    //                         ->get();
                    //     /* breadcrumb */
                    //     $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full);
                    //     $xhtml          = view('main.category.index', compact('item', 'products', 'breadcrumb', 'brands', 'categories'))->render();
                    //     break;
                    case 'page_info':
                        $flagMatch      = true;
                        /* thông tin brand */
                        $item           = Page::select('*')
                                            ->where('seo_id', $checkExists->id)
                                            ->with('seo', 'files')
                                            ->first();
                        /* breadcrumb */
                        $breadcrumb     = Url::buildBreadcrumb($checkExists->slug_full);
                        /* content */
                        $content        = Blade::render(Storage::get(config('main.storage.contentPage').$item->seo->slug.'.blade.php'));
                        /* page related */
                        $typePages      = Page::select('page_info.*')
                                            ->where('show_sidebar', 1)
                                            ->join('seo', 'seo.id', '=', 'page_info.seo_id')
                                            ->with('type')
                                            ->orderBy('seo.ordering', 'DESC')
                                            ->get()
                                            ->groupBy('type.id');
                        $xhtml          = view('wallpaper.page.index', compact('item', 'breadcrumb', 'content', 'typePages'))->render();
                        break;
                    // /* ===== TOUR CATEGORY INFO */
                    // case 'category_blog_info':
                    //     $flagMatch          = true;
                    //     $item               = CategoryBlog::select('*')
                    //                             ->whereHas('seo', function($q) use ($checkExists){
                    //                                 $q->where('slug', $checkExists->slug);
                    //                             })
                    //                             ->with('seo')
                    //                             ->first();
                    //     /* content */
                    //     // $content            = Blade::render(Storage::get(config('admin.storage.contentTour').$item->seo->slug.'.blade.php'));
                    //     /* breadcrumb */
                    //     $breadcrumb         = Url::buildBreadcrumb($checkExists->slug_full);
                    //     /* lấy id category và id category con (đệ quy) */
                    //     $arrayCategory      = CategoryBlog::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
                    //     $blogs              = Blog::select('*')
                    //         ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                    //             $query->whereIn('id', $arrayCategory);
                    //         })
                    //         ->with('seo')
                    //         ->get();
                    //     /* lấy thông tin category con (để phân biệt giao diện category cấp cuối và không phải cấp cuối) */
                    //     $infoCategoryChilds = CategoryBlog::select('*')
                    //         ->whereHas('seo', function($q) use ($item){
                    //             $q->where('parent', $item->seo->id);
                    //         })
                    //         ->with('seo')
                    //         ->get();
                    //     /* lấy tất cả category hiển thị theo cây */
                    //     $categoriesBlog     = CategoryBlog::getTreeCategory();
                    //     /* lấy danh mục sản phẩm */
                    //     $categories         = Category::getTreeCategory();
                    //     if(!empty($infoCategoryChilds)&&$infoCategoryChilds->isNotEmpty()){ /* trường hợp category chưa phải cấp cuối */
                    //         /* lấy danh sách từng blog theo category child */
                    //         foreach($infoCategoryChilds as $infoCategoryChild){
                    //             $infoCategoryChild->childs  = Blog::select('*')
                    //                 ->whereHas('categories.infoCategory', function($query) use($infoCategoryChild){
                    //                     $query->where('id', $infoCategoryChild->id);
                    //                 })
                    //                 ->with('seo')
                    //                 ->get();
                    //         }
                    //         $xhtml          = view('main.categoryBlog.indexParent', compact('item', 'breadcrumb', 'categoriesBlog', 'categories', 'infoCategoryChilds', 'blogs'))->render();
                    //     }else { /* trường hợp category là cấp cuối */
                    //         $xhtml          = view('main.categoryBlog.index', compact('item', 'breadcrumb', 'categoriesBlog', 'categories', 'blogs'))->render();
                    //     }
                    //     break;
                    // /* ===== BLOG INFO */
                    // case 'blog_info':
                    //     $flagMatch          = true;
                    //     $item               = Blog::select('*')
                    //                             ->whereHas('seo', function($q) use ($checkExists){
                    //                                 $q->where('slug', $checkExists->slug);
                    //                             })
                    //                             ->with('seo')
                    //                             ->first();
                    //     /* danh sách category của blog */
                    //     $arrayCategory      = [];
                    //     foreach($item->categories as $category) $arrayCategory[] = $category->infoCategory->id;
                    //     $blogRelates        = Blog::select('*')
                    //                             ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                    //                                 $query->whereIn('id', $arrayCategory);
                    //                             })
                    //                             ->where('id', '!=', $item->id)
                    //                             ->with('seo')
                    //                             ->get();
                    //     /* lấy tất cả category hiển thị theo cây */
                    //     $categoriesBlog     = CategoryBlog::getTreeCategory();
                    //     /* lấy tất cả categories sản phẩm */
                    //     $categories         = Category::getTreeCategory();
                    //     $content            = Blade::render(Storage::get(config('main.storage.contentBlog').$item->seo->slug.'.blade.php'));
                    //     $breadcrumb         = Url::buildBreadcrumb($checkExists->slug_full);
                    //     $xhtml              = view('main.blog.index', compact('item', 'breadcrumb', 'blogRelates', 'categories', 'categoriesBlog', 'content'))->render();
                    //     break;
                }
                /* Ghi dữ liệu - Xuất kết quả */
                if($flagMatch==true){
                    if(env('APP_CACHE_HTML')==true) Storage::put(config('main.cache.folderSave').$nameCache, $xhtml);
                    echo $xhtml;
                }else {
                    return \App\Http\Controllers\ErrorController::error404();
                }
            }
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
}
