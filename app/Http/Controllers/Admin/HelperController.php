<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Jobs\BuildScss;
use App\Helpers\Charactor;

use App\Models\Seo;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Page;
use App\Models\Product;

class HelperController extends Controller {

    public static function convertStrToSlug(Request $request){
        $string     = $request->get('string') ?? null;
        $language   = $request->get('language');
        $idPageParentVI     = $request->get('id_parent_vi') ?? 0;
        $response   = self::buildSlugFromTitle($string, $language, $idPageParentVI);
        echo $response;
    }

    public static function buildSlugFromTitle($title, $language, $idPageParentVI = 0){
        $response = '';
        if(!empty($title)&&$language){
            /* trường hợp có gửi vào id của trang cha (bảng vi) */
            if(!empty($idPageParentVI)){
                $infoPageParentVI   = self::getFullInfoPageByIdSeo($idPageParentVI);
                if(!empty($infoPageParentVI)) {
                    if($infoPageParentVI->seo->type=='category_info'||$infoPageParentVI->seo->type=='tag_info'){ /* trường hợp là category_info hay tag_info */
                        /* lấy slug của parent */
                        $slugParent         = null;
                        foreach($infoPageParentVI->seos as $seo){
                            if($seo->infoSeo->language==$language){
                                $slugParent = $seo->infoSeo->slug;
                                break;
                            }
                        }
                        /* lấy chiều ghép và tiến hành ghép */
                        $part       = Charactor::convertStrToUrl($title);
                        $charactor  = config('language.'.$language.'.flag_has_space_in_content')==true ? '-' : '';
                        if(config('language.'.$language.'.flag_join_left_to_right')==true){
                            $response = $slugParent.$charactor.$part;
                        }else {
                            $response = $part.$charactor.$slugParent;
                        }
                    } else { /* trường hợp là sản phẩm */
                        $response   = Charactor::convertStrToUrl($title).'-'.time();
                    }
                }
            } else { /* trường hợp không có trang cha */
                $response   = Charactor::convertStrToUrl($title);
            }
        }
        return $response;
    }

    public static function getFullInfoPageByIdSeo($idSeo){
        $response           = new \stdClass;
        /* kiểm tra xem loại nào */
        $infoSeo            = Seo::select('*')
                                ->where('id', $idSeo)
                                ->first();
        if(!empty($infoSeo)){
            $type               = $infoSeo->type;
            switch ($type) {
                case 'category_info':
                    $response   = Category::select('*')
                                    ->whereHas('seo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'tag_info':
                    $response   = Tag::select('*')
                                    ->whereHas('seo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'product_info':
                    $response   = Product::select('*')
                                    ->whereHas('seo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                case 'page_info':
                    $response   = Page::select('*')
                                    ->whereHas('seo', function($query) use($idSeo){
                                        $query->where('id', $idSeo);
                                    })
                                    ->with('seo', 'seos')
                                    ->first();
                    break;
                default:
                    break;
            }
        }
        return $response;
    }
    
    public static function removeUnicodeControlCharacters($string) {
        // Regular expression to match zero-width and formatting control characters
        $zeroWidthPattern = '/[\x{200B}-\x{200F}\x{2060}\x{FEFF}]/u';
        return preg_replace($zeroWidthPattern, '', $string);
    }
}
