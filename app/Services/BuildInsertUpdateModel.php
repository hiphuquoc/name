<?php

namespace App\Services;
use App\Models\Seo;
use Illuminate\Support\Facades\Auth;

class BuildInsertUpdateModel {
    public static function buildArrayTableSeo($dataForm, $type, $dataImage = null){
        $result                                 = [];
        if(!empty($dataForm)){
            $result['title']                    = $dataForm['name'] ?? null;
            $result['description']              = $dataForm['description'] ?? null;
            if(!empty($dataImage['filePathNormal'])) $result['image']           = $dataImage['filePathNormal'];
            if(!empty($dataImage['filePathSmall']))  $result['image_small']     = $dataImage['filePathSmall'];
            // page level
            $pageLevel                          = 1;
            $pageParent                         = 0;
            if(!empty($dataForm['parent'])){
                $infoPageParent                 = Seo::find($dataForm['parent']);
                $pageLevel                      = !empty($infoPageParent->level) ? ($infoPageParent->level+1) : $pageLevel;
                $pageParent                     = $infoPageParent->id;
            }
            $result['level']                    = $pageLevel;
            $result['parent']                   = $pageParent;
            $result['ordering']                 = $dataForm['ordering'] ?? null;
            $result['topic']                    = null;
            $result['seo_title']                = $dataForm['seo_title'] ?? $dataForm['title'] ?? null;
            $result['seo_description']          = $dataForm['seo_description'] ?? $dataForm['description'] ?? null;
            $result['slug']                     = $dataForm['slug'];
            /* slug full */
            $result['slug_full']                = Seo::buildFullUrl($dataForm['slug'], $pageLevel, $pageParent);
            /* link canonical */
            if(!empty($dataForm['link_canonical'])){
                $tmp                            = explode('/', $dataForm['link_canonical']);
                $tmp2                           = [];
                foreach($tmp as $t) if(!empty($t)) $tmp2[] = $t;
                $result['link_canonical']       = implode('/', $tmp2);
            }
            /* type */
            $result['type']                     = $type;
            $result['rating_author_name']       = 1;
            $result['rating_author_star']       = 5;
            $result['rating_aggregate_count']   = $dataForm['rating_aggregate_count'] ?? 0;
            $result['rating_aggregate_star']    = $dataForm['rating_aggregate_star'] ?? null;
            // $result['video']                    = $dataForm['video'] ?? null;
            $result['created_by']               = Auth::id() ?? 0;
        }
        return $result;
    }

    public static function buildArrayTableProductInfo($dataForm, $idSeo){
        $result                                 = [];
        $result['seo_id']                       = $idSeo;
        $result['brand_id']                     = $dataForm['brand'];
        $result['code']                         = $dataForm['code'];
        $result['name']                         = $dataForm['name'];
        $result['description']                  = $dataForm['description'];
        // $result['title_all']                    = $dataForm['title_all'];
        // $result['price_all']                    = $dataForm['price_all'];
        // $result['price_all_before_promotion']   = $dataForm['price_all_before_promotion'] ?? null;
        // $result['sale_off_all']                 = 0;
        // if(!empty($dataForm['price_all_before_promotion'])){
        //     $result['sale_off_all'] = (($dataForm['price_all_before_promotion'] - $dataForm['price_all'])/$dataForm['price_all_before_promotion'])*100;
        // }
        return $result;
    }

    public static function buildArrayTableProductPrice($dataForm, $idProduct){
        $result                                 = [];
        if(!empty($dataForm['name'])&&!empty($dataForm['price'])&&!empty($idProduct)){
            $result['product_info_id']  = $idProduct;
            $result['name']             = $dataForm['name'];
            $result['description']      = $dataForm['description'] ?? null;
            $result['price']            = $dataForm['price'];
            $result['price_origin']     = $dataForm['price_origin'] ?? null;
            $result['price_before_promotion']   = $dataForm['price_before_promotion'] ?? null;
            $result['sale_off']         = 0;
            if(!empty($dataForm['price_before_promotion'])){
                $result['sale_off'] = (($dataForm['price_before_promotion'] - $dataForm['price'])/$dataForm['price_before_promotion'])*100;
            }
            $result['instock']          = $dataForm['instock'] ?? null;
        }
        return $result;
    }

    public static function buildArrayTableCustomerInfo($dataForm){
        $result                                 = [];
        if(!empty($dataForm['name'])&&!empty($dataForm['phone'])){
            if(!empty($dataForm['prefix_name'])) $result['prefix_name']  = $dataForm['prefix_name'];
            $result['name']             = $dataForm['name'];
            $result['phone']            = $dataForm['phone'];
            if(!empty($dataForm['zalo'])) $result['zalo']  = $dataForm['zalo'];
            if(!empty($dataForm['email'])) $result['email']  = $dataForm['email'];
        }
        return $result;
    }

    public static function buildArrayTableCustomerAddress($dataForm, $idCustomer){
        $result                         = [];
        if(!empty($dataForm['address'])&&!empty($dataForm['province_info_id'])&&!empty($dataForm['district_info_id'])){
            $result['customer_info_id'] = $idCustomer;
            $result['address']          = $dataForm['address'];
            $result['province_info_id'] = $dataForm['province_info_id'];
            $result['district_info_id'] = $dataForm['district_info_id'];
        }
        return $result;
    }

    public static function buildArrayTableOrderInfo($dataForm, $idCustomer, $products){
        $result                             = [];
        $result['code']                     = strtoupper(\App\Helpers\Charactor::randomString(8));
        $result['customer_info_id']         = $idCustomer;
        $result['product_count']            = 0;
        $result['product_cash']             = 0;
        foreach($products as $product){
            $result['product_count']        += $product->cart['quantity'];
            $result['product_cash']         += $product->cart['quantity']*$product->price->price;
        }
        $result['ship_cash']                = $dataForm['ship_cash'];
        $result['total']                    = $result['ship_cash']+$result['product_cash'];
        $result['payment_method_info_id']   = $dataForm['payment_method_info_id'];
        $result['name']                     = $dataForm['name'];
        $result['phone']                    = $dataForm['phone'];
        $result['address']                  = $dataForm['address'];
        $result['province_info_id']         = $dataForm['province_info_id'];
        $result['district_info_id']         = $dataForm['district_info_id'];
        $result['ship_note']                = $dataForm['ship_note'] ?? null;
        $result['note']                     = $dataForm['note'] ?? null;
        return $result;
    }

    public static function buildArrayTableCategoryBlogInfo($dataForm, $seoId = null){
        $result     = [];
        if(!empty($dataForm)){
            if(!empty($seoId)) $result['seo_id'] = $seoId;
            $result['name']             = $dataForm['name'] ?? null;
            $result['description']      = $dataForm['description'] ?? null;
        }
        return $result;
    }

    public static function buildArrayTableBlogInfo($dataForm, $seoId = null){
        $result     = [];
        if(!empty($dataForm)){
            if(!empty($seoId)) $result['seo_id'] = $seoId;
            $result['name']             = $dataForm['name'] ?? null;
            $result['description']      = $dataForm['description'] ?? null;
            $result['outstanding']          = 0;
            if(!empty($dataForm['outstanding'])) {
                if($dataForm['outstanding']=='on') $result['outstanding'] = 1;
            }
            $result['note']             = $dataForm['note'] ?? null;
        }
        return $result;
    }

    public static function buildArrayTableSellerInfo($dataForm){
        $result     = [];
        if(!empty($dataForm)){
            $result['prefix_name']      = $dataForm['prefix_name'] ?? null;
            $result['name']             = $dataForm['name'];
            $result['phone']            = $dataForm['phone'];
            $result['zalo']             = $dataForm['zalo'] ?? null;
            $result['email']            = $dataForm['email'] ?? null;
            $result['address']          = $dataForm['address'] ?? null;
            $result['province_info_id'] = $dataForm['province_info_id'] ?? null;
            $result['district_info_id'] = $dataForm['district_info_id'] ?? null;
        }
        return $result;
    }
}