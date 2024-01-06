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
        $loaded             = $request->get('loaded');
        $total              = $request->get('total');
        $id                 = $request->get('id');
        $type               = $request->get('type');
        $search             = $request->get('search') ?? null;
        $requestLoad        = $request->get('request_load') ?? 10;
        if($loaded<$total){
            /* content product */
            $arrayCategory      = null;
            if($type=='category_info'){
                $item           = Category::select('*')
                                    ->where('id', $id)
                                    ->first();
                $arrayCategory  = Category::getArrayIdCategoryRelatedByIdCategory($item, [$item->id]);
            }
            $products       = Product::select('product_info.*')
                                ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                                ->whereHas('prices.wallpapers', function($query){

                                })
                                ->when(!empty($keySearch), function($query) use($search){
                                    $query->where('code', 'like', '%'.$search.'%')
                                        ->orWhere('name', 'like', '%'.$search.'%')
                                        ->orWhere('en_name', 'like', '%'.$search.'%');
                                })
                                ->when($type=='category_info', function($query) use($arrayCategory){
                                    $query->whereHas('categories.infoCategory', function($query) use($arrayCategory){
                                        $query->whereIn('id', $arrayCategory);
                                    });
                                })
                                ->when($type=='style_info', function($query) use($id){
                                    $query->whereHas('styles.infoStyle', function($query) use($id){
                                        $query->where('id', $id);
                                    });
                                })
                                ->when($type=='event_info', function($query) use($id){
                                    $query->whereHas('events.infoEvent', function($query) use($id){
                                        $query->where('id', $id);
                                    });
                                })
                                ->with('seo', 'en_seo', 'prices')
                                ->skip($request->get('loaded'))
                                ->take($requestLoad)
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
            /* phần tính toán */
            $loaded         = $request->get('loaded') + $products->count();
        }
        $response['content']    = $xhtmlProduct;
        $response['loaded']     = $loaded;
        return json_encode($response);
    }

}
