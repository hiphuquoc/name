<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use App\Models\Category;

class CategoryController extends Controller {

    public static function loadMore(Request $request){
        $xhtmlProduct       = null;
        $requestLoad        = $request->get('request_load') ?? 10;
        $tmp                = !empty($request->get('array_product_info_id')) ? json_decode($request->get('array_product_info_id'), true) : [];
        if(!empty($tmp)){
            $arrayIdProductLoad = array_slice($tmp, 0, $requestLoad);
            $arrayIdProductSave = array_slice($tmp, $requestLoad);
            /* content product */
            $products       = Product::select('product_info.*')
                                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                ->whereIn('product_info.id', $arrayIdProductLoad)
                                ->whereHas('prices.wallpapers', function($query){

                                })
                                ->with('seo', 'en_seo', 'prices')
                                ->orderBy('seo.ordering', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->get();
            $language       = !empty($request->get('language')) ? $request->get('language') : 'vi';
            foreach($products as $product){
                if(!empty($request->get('view_by'))&&$request->get('view_by')=='set'){
                    /* chế độ xem từng bộ */
                    $xhtmlProduct   .= view('wallpaper.template.wallpaperItem', [
                        'product'   => $product,
                        'language'  => $language,
                        'lazyload'  => true
                    ])->render();
                }else {
                    /* chế độ xem từng ảnh */
                    foreach($product->prices as $price){
                        foreach($price->wallpapers as $wallpaper){
                            $link           = empty($language)||$language=='vi' ? '/'.$product->seo->slug_full : '/'.$product->en_seo->slug_full;
                            $productName    = $product->name ?? null;
                            $lazyload       = true;
                            $xhtmlProduct   .= view('wallpaper.template.perWallpaperItem', [
                                'idProduct' => $product->id,
                                'idPrice'   => $price->id,
                                'wallpaper' => $wallpaper, 
                                'productName'   => $productName,
                                'link'      => $link,
                                'language'  => $language,
                                'lazyload'  => $lazyload
                            ]);
                        }
                    }
                }
            }
        }
        $response['content']    = $xhtmlProduct;
        $response['array_product_info_id']     = json_encode($arrayIdProductSave) ?? '';
        return json_encode($response);
    }

}
