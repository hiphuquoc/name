<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use App\Helpers\Url;
use App\Models\Product;
use App\Models\Page;

class PageController extends Controller{

    public static function saleOff(){
        /* thông tin Page */
        $item           = Page::select('*')
            ->whereHas('seo', function($query){
                $query->where('slug', 'hinh-nen-dien-thoai-khuyen-mai');
            })
            ->with('seo', 'files')
            ->first();
            $flagMatch      = true;
        /* danh sách product => lấy riêng để dễ truyền vào template */
        $products       = Product::select('*')
                            ->whereHas('prices', function($query) {
                                $query->where('sale_off', '>', 0);
                            })
                            ->with('seo', 'files', 'prices', 'contents', 'categories', 'brand.seo')
                            ->orderBy('id', 'DESC')
                            ->skip(0)
                            ->take(5)
                            ->get();
        $totalProduct   = Product::select('*')
                            ->whereHas('prices', function($query) {
                                $query->where('sale_off', '>', 0);
                            })
                            ->count();
        /* breadcrumb */
        $breadcrumb         = Url::buildBreadcrumb($item->seo->slug_full);
        return view('wallpaper.category.promotion', compact('item', 'products', 'totalProduct', 'breadcrumb'));
    }

    public static function searchProduct(Request $request){
        $keySearch      = $request->get('key_search') ?? null;
        $keySearch      = \App\Helpers\Charactor::convertStringSearch($request->get('key_search'));
        /* thông tin Page */
        $pathUrl        = substr(parse_url(url()->current())['path'], 1);
        $item           = Page::select('*')
            ->whereHas('seo', function($query) use($pathUrl){
                $query->where('slug_full', $pathUrl);
            })
            ->with('seo', 'files')
            ->first();
        if(!empty($item)){
            /* danh sách product */
            $products       =  Product::select('*')
                ->where('name', 'like', '%'.$keySearch.'%')
                ->with('seo', 'files', 'prices', 'contents', 'categories', 'brand.seo')
                ->orderBy('id', 'DESC')
                ->skip(0)
                ->take(5)
                ->get();
            $totalProduct   =  Product::select('product_info.*')
                ->where('name', 'like', '%'.$keySearch.'%')
                ->count();
            /* breadcrumb */
            $breadcrumb     = Url::buildBreadcrumb($item->seo->slug_full);
            $titlePage      = $item->name ?? $item->seo->title ?? null;
            return view('wallpaper.category.search', compact('item', 'titlePage', 'products', 'totalProduct', 'breadcrumb'));
        }
        return redirect()->route('main.home');
    }

}
