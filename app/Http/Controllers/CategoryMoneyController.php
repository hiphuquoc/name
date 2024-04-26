<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use App\Models\FreeWallpaper;
use Illuminate\Support\Facades\Auth;

class CategoryMoneyController extends Controller {

    public static function loadMoreWallpaper(Request $request){
        $response           = [];
        $content            = '';
        $language           = $request->session()->get('language') ?? 'vi';
        $viewBy             = Cookie::get('view_by') ?? 'each_set';
        $params             = [];
        /* data params */
        $params['key_search']               = null;
        $params['loaded']                   = $request->get('loaded');
        $params['request_load']             = $request->get('request_load');
        $params['array_category_info_id']   = json_decode($request->get('array_category_info_id'));
        $params['sort_by']                  = Cookie::get('sort_by') ?? config('main.sort_type')[0]['key'];
        $params['filters']                  = $request->get('filters') ?? [];
        $tmp                                = self::getWallpapers($params, $language);
        foreach($tmp['wallpapers'] as $wallpaper){
            if($viewBy=='each_set'){
                $content    .= view('wallpaper.template.wallpaperItem', [
                    'product'   => $wallpaper,
                    'language'  => $language,
                    'lazyload'  => true
                ])->render();
            }else {
                $link           = empty($language)||$language=='vi' ? '/'.$wallpaper->seo->slug_full : '/'.$wallpaper->en_seo->slug_full;
                $wallpaperName    = $wallpaper->name ?? null;
                foreach($wallpaper->prices as $price){
                    foreach($price->wallpapers as $w){
                        $content .= view('wallpaper.template.perWallpaperItem', [
                            'idProduct'     => $w->id,
                            'idPrice'       => $price->id,
                            'wallpaper'     => $w, 
                            'productName'   => $wallpaperName,
                            'link'          => $link,
                            'language'      => $language,
                            'lazyload'      => true
                        ]);
                    }
                }
            }
        }
        $response['content']    = $content;
        $response['loaded']     = $tmp['loaded'];
        $response['total']      = $tmp['total'];
        return json_encode($response);
    }

    public static function getWallpapers($params, $language){
        $keySearch      = $params['key_search'] ?? null;
        $filters        = $params['filters'] ?? [];
        $sortBy         = $params['sort_by'] ?? null;
        $loaded         = $params['loaded'] ?? 0;
        $arrayIdCategory = $params['array_category_info_id'] ?? [];
        $requestLoad    = $params['request_load'] ?? 10;
        $response       = [];
        $wallpapers = Product::select('product_info.*')
                            ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                            ->whereHas('prices.wallpapers', function() {})
                            ->whereHas('seos.infoSeo', function($query) use ($language) {
                                $query->where('language', $language);
                            })
                            ->when(!empty($keySearch), function($query) use ($keySearch) {
                                $query->where('code', 'like', '%' . $keySearch . '%')
                                    ->orWhere('name', 'like', '%' . $keySearch . '%')
                                    ->orWhere('en_name', 'like', '%' . $keySearch . '%');
                            })
                            ->when(!empty($filters), function($query) use ($filters) {
                                foreach ($filters as $filter) {
                                    $query->whereHas('categories.infoCategory', function($query) use ($filter) {
                                        $query->where('id', $filter);
                                    });
                                }
                            })
                            ->when(!empty($arrayIdCategory), function($query) use ($arrayIdCategory) {
                                $query->whereHas('categories', function($query) use ($arrayIdCategory) {
                                    $query->whereIn('category_info_id', $arrayIdCategory);
                                });
                            })
                            ->when(empty($sortBy), function($query) {
                                $query->orderBy('id', 'DESC');
                            })
                            ->when($sortBy == 'new' || $sortBy == 'propose', function($query) {
                                $query->orderBy('id', 'DESC');
                            })
                            ->when($sortBy == 'favourite', function($query) {
                                $query->orderBy('heart', 'DESC')
                                    ->orderBy('id', 'DESC');
                            })
                            ->when($sortBy == 'old', function($query) {
                                $query->orderBy('id', 'ASC');
                            })
                            ->with(['seos.infoSeo' => function($query) use ($language) {
                                $query->where('language', $language);
                            }, 'seo', 'prices'])
                            ->orderBy('seo.ordering', 'DESC')
                            ->orderBy('id', 'DESC')
                            ->skip($loaded)
                            ->take($requestLoad)
                            ->get();
        $total          = Product::select('product_info.*')
                            ->join('seo', 'seo.id', '=', 'product_info.seo_id')
                            ->whereHas('prices.wallpapers', function(){})
                            ->whereHas('seos.infoSeo', function($query) use ($language) {
                                $query->where('language', $language);
                            })
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
                            ->when(!empty($arrayIdCategory), function($query) use($arrayIdCategory){
                                $query->whereHas('categories', function($query) use($arrayIdCategory) {
                                    $query->whereIn('category_info_id', $arrayIdCategory);
                                });
                            })
                            ->count();
        $response['wallpapers'] = $wallpapers;
        $response['total']      = $total;
        $response['loaded']     = $loaded + $requestLoad;
        return $response;
    }
}
