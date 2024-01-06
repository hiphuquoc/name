<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BuildInsertUpdateModel;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductPrice;
use App\Models\Wallpaper;
use App\Models\RelationProductPriceWallpaperInfo;

class ProductPriceController extends Controller {

    public function loadWallpaperByProductPrice(Request $request){
        $response = '';
        if(!empty($request->get('product_price_id'))){
            $idProductPrice = $request->get('product_price_id');
            $wallpapers     = Wallpaper::select('wallpaper_info.*')
                                ->join('relation_product_price_wallpaper_info', 'wallpaper_info.id', '=', 'relation_product_price_wallpaper_info.wallpaper_info_id')
                                ->where('relation_product_price_wallpaper_info.product_price_id', $idProductPrice)
                                ->get();
            $response = view('admin.product.showWallpaper', [
                'wallpapers'        => $wallpapers,
                'idProductPrice'    => $idProductPrice
            ])->render();
        }
        return $response;
    }

    public function addWallpaperToProductPrice(Request $request){
        $flag       = false;
        // if(!empty($request->get('wallpaper_id'))&&!empty($request->get('product_price_id'))&&!empty($request->get('action'))){
            if($request->get('action')=='create'){
                $infoRelation   = RelationProductPriceWallpaperInfo::select('*')
                                    ->where('wallpaper_info_id', $request->get('wallpaper_id'))
                                    ->where('product_price_id', $request->get('product_price_id'))
                                    ->first();
                if(empty($infoRelation)){
                    $id = RelationProductPriceWallpaperInfo::insertItem([
                        'wallpaper_info_id' => $request->get('wallpaper_id'),
                        'product_price_id'  => $request->get('product_price_id')
                    ]);
                }
                if(!empty($id)||!empty($infoRelation)) $flag = true;
            }else if($request->get('action')=='delete'){
                $flag   = RelationProductPriceWallpaperInfo::select('*')
                            ->where('wallpaper_info_id', $request->get('wallpaper_id'))
                            ->where('product_price_id', $request->get('product_price_id'))
                            ->delete();
            }
        return $flag;
    }

    public function deleteWallpaperToProductPrice(Request $request){
        $flag       = false;
        if(!empty($request->get('wallpaper_id'))&&!empty($request->get('product_price_id'))){
            $flag   = RelationProductPriceWallpaperInfo::select('*')
                        ->where('wallpaper_info_id', $request->get('wallpaper_id'))
                        ->where('product_price_id', $request->get('product_price_id'))
                        ->delete();
        }
        return $flag;
    }
}
