<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use App\Models\FreeWallpaper;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller {

    public static function loadMoreWallpaper(Request $request){
        $response           = [];
        $content            = '';
        $language           = Cookie::get('language') ?? 'vi';
        $loaded             = $request->get('loaded');
        $requestLoad        = $request->get('request_load');
        $arrayIdCategory    = json_decode($request->get('array_category_info_id'));
        $sortBy             = Cookie::get('sort_by') ?? config('main.sort_type')[0]['key'];
        // $user           = Auth::user();
        /* filter nếu có */
        $filters            = $request->get('filters') ?? [];
        /* tìm kiếm bằng key_search */
        $keySearch          = null;
        $wallpapers         = $wallpapers     = Product::select('product_info.*')
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
                            ->skip($loaded)
                            ->take($requestLoad)
                            ->get();
        foreach($wallpapers as $wallpaper){
            $content    .= view('wallpaper.template.wallpaperItem', [
                'product'   => $wallpaper,
                'language'  => $language,
                'lazyload'  => true
            ])->render();
        }
        $response['content']    = $content;
        $response['loaded']     = $loaded + $requestLoad;
        return json_encode($response);
    }

    public static function loadmoreFreeWallpapers(Request $request){
        /* tìm kiếm bằng feeling */
        $searchFeeling = $request->get('search_feeling') ?? [];
        foreach($searchFeeling as $feeling){
            if($feeling=='all'){ /* trường hợp tìm kiếm có all thì clear */
                $searchFeeling = [];
                break;
            }
        }
        $response           = [];
        $content            = '';
        if(!empty($request->get('total'))){
            $language       = Cookie::get('language') ?? 'vi';
            $loaded         = $request->get('loaded');
            $requestLoad    = $request->get('requestLoad');
            $arrayIdCategory = json_decode($request->get('arrayIdCategory'));
            $typeWhere      = $request->get('typeWhere') ?? 'or';
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
                                ->skip($loaded)
                                ->take($requestLoad)
                                ->get();
            foreach($wallpapers as $wallpaper){
                $content    .= view('wallpaper.free.item', compact('wallpaper', 'language', 'user'))->render();
            }
        }
        $response['content']    = $content;
        $response['loaded']     = $loaded + $requestLoad;
        return json_encode($response);
    }
}
