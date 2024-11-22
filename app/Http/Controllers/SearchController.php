<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\HelperController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic;
use App\Models\District;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Blog;
use App\Models\FreeWallpaper;
use App\Models\RegistryEmail;
use App\Models\RelationFreeWallpaperUser;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Services\BuildInsertUpdateModel;
// use SebastianBergmann\Type\FalseType;

class SearchController extends Controller {

    public static function searchAjax(Request $request){
        $searchType = $request->get('search_type');
        $keySearch  = \App\Helpers\Charactor::convertStringSearch($request->get('search'));
        $language   = $request->get('language');
        switch ($searchType) {
            case 'category_info':
                /* tìm kiếm danh mục */
                $xhtmlContent = self::searchCategoryInfo($keySearch, $language);
                break;
            case 'paid_wallpaper':
                /* tìm kiếm hình nền trả phí */
                $xhtmlContent = self::searchPremiumWallpaper($keySearch, $language);
                break;
            case 'free_wallpaper':
                /* tìm kiếm hình nền miễn phí */
                $xhtmlContent = self::searchFreeWallpaper($keySearch, $language);
                break;
            case 'article':
                /* tìm kiếm bài blog */
                $xhtmlContent = self::searchBlogInfo($keySearch, $language);
                break;
            default:
                $xhtmlContent = null;
                break;
        }
        /* thêm icon loadding => cho lần load tiếp theo */
        // $xhtmlContent         .= '<div id="js_searchAjax_iconLoading" class="loadingBox"><span class="loadingIcon"></span></div>';
        echo $xhtmlContent;
    }

    private static function searchPremiumWallpaper($keySearch, $language){
        $products           = Product::select('product_info.*')
            ->whereHas('prices.wallpapers', function(){

            })
            ->whereHas('seos.infoSeo', function($query) use($keySearch, $language){
                $query->where('title', 'like', '%'.$keySearch.'%');
            })
            ->orWhere('code', 'like', '%'.$keySearch.'%')
            ->get();
        $count              = $products->count();
        // $count              = Product::select('product_info.*')
        //     ->whereHas('prices.wallpapers', function(){
                    
        //     })
        //     ->whereHas('seos.infoSeo', function($query) use($keySearch, $language){
        //         $query->where('title', 'like', '%'.$keySearch.'%');
        //     })
        //     ->orWhere('code', 'like', '%'.$keySearch.'%')
        //     ->count();
        $response           = '';
        if(!empty($products)&&$products->isNotEmpty()){
            $response       = view('wallpaper.search.premiumWallpaper', compact('products', 'language', 'keySearch', 'count'));
        }else {
            $response       = view('wallpaper.template.emptySearch', compact('language'))->render();
        }
        return $response;
   }

    private static function searchCategoryInfo($keySearch, $language){
        $categories         = Category::select('*')
            ->whereHas('seos.infoSeo', function($query) use($keySearch, $language){
                $query->where('title', 'like', '%'.$keySearch.'%')
                    ->where('language', $language);
            })
            ->get();
        $tags               = Tag::select('*')
            ->whereHas('seos.infoSeo', function ($query) use ($keySearch, $language) {
                $query->where('title', 'like', '%' . $keySearch . '%')
                    ->where('language', $language);
            })
            ->get();
        /* Gộp kết quả từ Category và Tag thành một collection */
        $categories         = $categories->concat($tags);
        $count              = $categories->count();
        /* lấy giao diện */
        $response           = '';
        if(!empty($categories)&&$categories->isNotEmpty()){
            $response       = view('wallpaper.search.categoryInfo', compact('categories', 'language', 'keySearch', 'count'));
        }else {
            $response       = view('wallpaper.template.emptySearch', compact('language'))->render();
        }
        return $response;
    }

    private static function searchFreeWallpaper($keySearch, $language){
        $response = '<div class="emptySearchBox">Tinh năng này đang được bảo trì, vui lòng sử dụng sau!</div>';
        return $response;
    }

    private static function searchBlogInfo($keySearch, $language){
        $blogs              = Blog::select('*')
            ->whereHas('seos.infoSeo', function($query) use($keySearch, $language){
                $query->where('title', 'like', '%'.$keySearch.'%')
                    ->where('language', $language);
            })
            ->get();
        $count              = $blogs->count();
        $response           = '';
        if(!empty($blogs)&&$blogs->isNotEmpty()){
            $response       = view('wallpaper.search.blogInfo', compact('blogs', 'language', 'keySearch', 'count'));
        }else {
            $response       = view('wallpaper.template.emptySearch', compact('language'))->render();
        }
        return $response;
    }
}
