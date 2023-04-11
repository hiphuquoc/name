<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class CategoryController extends Controller {

    public static function loadMore(Request $request){
        $xhtmlProduct       = null;
        $loaded             = $request->get('loaded') ?? 0;
        if(!empty($request->get('total'))&&!empty($request->get('key_category'))){
            /* content product */
            $arrayCategory  = json_decode($request->get('key_category'), true);
            $requestLoad    = $request->get('request_load') ?? 5;
            $products       = Product::select('*')
                                ->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                    $query->whereIn('id', $arrayCategory);
                                })
                                ->orderBy('id', 'DESC')
                                ->skip($request->get('loaded'))
                                ->take($requestLoad)
                                ->get();
            foreach($products as $product){
                $xhtmlProduct   .= view('wallpaper.template.wallpaperItem', [
                    'product'   => $product,
                    'type'      => 'ajax'
                ])->render();
            }
            /* phần tính toán */
            $loaded         = $request->get('loaded') + $products->count();
        }
        $response['content']    = $xhtmlProduct;
        $response['loaded']     = $loaded;
        return json_encode($response);
    }

    public static function loadMorePromotion(Request $request){
        $xhtmlProduct       = null;
        $loaded             = $request->get('loaded') ?? 0;
        if(!empty($request->get('total'))){
            /* content product */
            $requestLoad    = $request->get('request_load') ?? 5;
            $products       = Product::select('*')
                                ->whereHas('prices', function($query){
                                    $query->where('sale_off', '>', 0);
                                })
                                ->orderBy('id', 'DESC')
                                ->skip($request->get('loaded'))
                                ->take($requestLoad)
                                ->get();
            foreach($products as $product){
                $xhtmlProduct   .= view('wallpaper.template.wallpaperItem', [
                    'product'   => $product,
                    'type'      => 'ajax'
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
