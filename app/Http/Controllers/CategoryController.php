<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use App\Models\Product;
use App\Models\FreeWallpaper;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller {

    public static function loadmoreFreeWallpapers(Request $request){
        /* tìm kiếm bằng feeling */
        $searchFeeling = $request->get('search_feeling') ?? [];
        // foreach($searchFeeling as $feeling){
        //     if($feeling=='all'){ /* trường hợp tìm kiếm có all thì clear */
        //         $searchFeeling = [];
        //         break;
        //     }
        // }
        $response           = [];
        $content            = '';
        $params             = [];
        if(!empty($request->get('total'))){
            $params['loaded']                   = $request->get('loaded');
            $params['request_load']             = $request->get('requestLoad');
            $params['array_category_info_id']   = json_decode($request->get('array_category_info_id'));
            $params['sort_by']                  = Cookie::get('sort_by') ?? null;
            $params['filters']                  = $request->get('filters') ?? [];
            $params['id_not']                   = $request->get('idNot') ?? 0;
            $tmp                                = self::getFreeWallpapers($params);
            $user           = Auth::user();
            $language       = $request->session()->get('language') ?? 'vi';
            foreach($tmp['wallpapers'] as $wallpaper){
                $content    .= view('wallpaper.category.item', compact('wallpaper', 'language', 'user'))->render();
            }
        }
        $response['content']    = $content;
        $response['loaded']     = $tmp['loaded'] ?? $request->get('loaded') ?? 0;
        $response['total']      = $tmp['total'] ?? $request->get('total') ?? 0;
        return json_encode($response);
    }

    public static function getFreeWallpapers($params){
        $idNot          = $params['id_not'] ?? 0;
        $keySearch      = $params['key_search'] ?? null;
        $filters        = $params['filters'] ?? [];
        $sortBy         = $params['sort_by'] ?? null;
        $loaded         = $params['loaded'] ?? 0;
        $arrayIdCategory = $params['array_category_info_id'] ?? [];
        $requestLoad    = $params['request_load'] ?? 10;
        $response       = [];
        $wallpapers     = FreeWallpaper::select('*')
                            ->when(!empty($idNot), function($query) use($idNot){
                                $query->where('id', '!=', $idNot);
                            })
                            ->when(!empty($arrayIdCategory), function($query) use($arrayIdCategory){
                                $query->whereHas('categories', function($query) use($arrayIdCategory) {
                                    $query->whereIn('category_info_id', $arrayIdCategory);
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
                            ->skip($loaded)
                            ->take($requestLoad)
                            ->get();
        $total          = FreeWallpaper::select('*')
                            ->when(!empty($idNot), function($query) use($idNot){
                                $query->where('id', '!=', $idNot);
                            })
                            ->when(!empty($arrayIdCategory), function($query) use($arrayIdCategory){
                                $query->whereHas('categories', function($query) use($arrayIdCategory) {
                                    $query->whereIn('category_info_id', $arrayIdCategory);
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
        $response['wallpapers'] = $wallpapers;
        $response['total']      = $total;
        $response['loaded']     = $loaded + $requestLoad;
        return $response;
    }
}
