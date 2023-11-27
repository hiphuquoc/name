<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;

class CategoryController extends Controller {

    public static function loadMore(Request $request){
        $xhtmlProduct       = null;
        $loaded             = $request->get('loaded') ?? 0;
        if(!empty($request->get('total'))&&!empty($request->get('key_category'))){
            /* content product */
            $arrayCategory  = json_decode($request->get('key_category'), true);
            $requestLoad    = $request->get('request_load') ?? 5;
            $products       = Product::select('product_info.*')
                                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                    $query->whereIn('id', $arrayCategory);
                                })
                                ->orderBy('seo.ordering', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->skip($request->get('loaded'))
                                ->take($requestLoad)
                                ->get();
            $language       = !empty($request->get('language')) ? $request->get('language') : 'vi';
            foreach($products as $product){
                $xhtmlProduct   .= view('wallpaper.template.wallpaperItem', [
                    'product'   => $product,
                    'language'  => $language,
                    'lazyload'  => true
                ])->render();
            }
            /* phần tính toán */
            $loaded         = $request->get('loaded') + $products->count();
        }
        $response['content']    = $xhtmlProduct;
        $response['loaded']     = $loaded;
        return json_encode($response);
    }

}
