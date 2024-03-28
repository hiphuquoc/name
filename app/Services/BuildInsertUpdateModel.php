<?php

namespace App\Services;
use App\Models\Seo;
use App\Models\EnSeo;
use Illuminate\Support\Facades\Auth;

class BuildInsertUpdateModel {
    public static function buildArrayTableSeo($dataForm, $type, $dataImage = null){
        $result                                 = [];
        if(!empty($dataForm)){
            $result['title']                    = $dataForm['title'] ?? null;
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
            if(!empty($dataForm['ordering'])) $result['ordering'] = $dataForm['ordering'];
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
            }else {
                $result['link_canonical']       = null;
            }
            /* type */
            if(!empty($type)) $result['type']   = $type;
            $result['rating_author_name']       = 1;
            $result['rating_author_star']       = 5;
            $result['rating_aggregate_count']   = $dataForm['rating_aggregate_count'] ?? 0;
            $result['rating_aggregate_star']    = $dataForm['rating_aggregate_star'] ?? null;
            $result['language']                 = $dataForm['language'] ?? 'vi';
            $result['created_by']               = Auth::id() ?? 0;
        }
        return $result;
    }

    public static function buildArrayTableEnSeo($dataForm, $type, $dataImage = null){
        $result                                 = [];
        if(!empty($dataForm)){
            $result['title']                    = $dataForm['en_name'] ?? null;
            $result['description']              = $dataForm['en_description'] ?? null;
            if(!empty($dataImage['filePathNormal'])) $result['image']           = $dataImage['filePathNormal'];
            if(!empty($dataImage['filePathSmall']))  $result['image_small']     = $dataImage['filePathSmall'];
            // page level
            $pageLevel                          = 1;
            $pageParent                         = 0;
            if(!empty($dataForm['parent'])){
                $idParentVi                     = $dataForm['parent'];
                $infoParentEn                   = EnSeo::select('*')
                                                    ->whereHas('seo.infoSeo', function($query) use($idParentVi){
                                                        $query->where('id', $idParentVi);
                                                    })
                                                    ->first();
                $pageLevel                      = !empty($infoParentEn->level) ? ($infoParentEn->level+1) : $pageLevel;
                $pageParent                     = $infoParentEn->id;
            }
            $result['level']                    = $pageLevel;
            $result['parent']                   = $pageParent;
            $result['ordering']                 = $dataForm['ordering'] ?? null;
            $result['topic']                    = null;
            $result['seo_title']                = $dataForm['en_seo_title'] ?? $dataForm['en_title'] ?? null;
            $result['seo_description']          = $dataForm['en_seo_description'] ?? $dataForm['en_description'] ?? null;
            $result['slug']                     = $dataForm['en_slug'];
            /* slug full */
            $result['slug_full']                = EnSeo::buildFullUrl($dataForm['en_slug'], $pageLevel, $pageParent);
            /* link canonical */
            if(!empty($dataForm['en_link_canonical'])){
                $tmp                            = explode('/', $dataForm['en_link_canonical']);
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

    public static function buildArrayTableProductInfo($dataForm, $seoId){
        $result                                 = [];
        $result['code']                         = $dataForm['code'];
        if(!empty($seoId)) $result['seo_id']    = $seoId;
        $result['price']                        = $dataForm['price'];
        $result['price_before_promotion']       = $dataForm['price_before_promotion'] ?? null;
        if(!empty($dataForm['price_before_promotion'])){
            $result['sale_off'] = round((($dataForm['price_before_promotion'] - $dataForm['price'])/$dataForm['price_before_promotion'])*100);
        }
        return $result;
    }

    public static function buildArrayTableProductPrice($dataForm, $idProduct, $type = 'insert'){
        $result                                 = [];
        if(!empty($dataForm['code_name'])&&!empty($dataForm['price'])&&!empty($idProduct)){
            $result['code_name']        = $dataForm['code_name'];
            $result['product_info_id']  = $idProduct;
            $result['price']            = $dataForm['price'];
            $result['price_origin']     = $dataForm['price_origin'] ?? null;
            $result['price_before_promotion']   = $dataForm['price_before_promotion'] ?? null;
            $result['sale_off']         = 0;
            if(!empty($dataForm['price_before_promotion'])){
                $result['sale_off'] = round((($dataForm['price_before_promotion'] - $dataForm['price'])/$dataForm['price_before_promotion'])*100);
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

    public static function buildArrayTableOrderInfo($dataForm, $idCustomer, $detailCart){
        $result                             = [];
        $result['code']                     = strtoupper(\App\Helpers\Charactor::randomString(15));
        $result['customer_info_id']         = $idCustomer ?? null;
        $result['product_count']            = $detailCart['count'];
        $result['product_cash']             = $detailCart['into_money'];
        $result['ship_cash']                = $detailCart['fee'] ?? 0;
        $result['total']                    = $detailCart['total'];
        $result['payment_method_info_id']   = $dataForm['payment_method_info_id'];
        $result['email']                    = $dataForm['email'] ?? null;
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