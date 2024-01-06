<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CookieController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Models\Page;
use App\Models\Product;
use App\Models\PaymentMethod;

class CartController extends Controller{

    public static function index(Request $request){
        $language       = 'vi';
        SettingController::settingLanguage($language);
        $item           = Page::select('*')
                            ->whereHas('seo', function($query){
                                $query->where('slug', 'gio-hang');
                            })
                            ->with('seo', 'en_seo', 'type')
                            ->first();
        $products       = \App\Http\Controllers\CartController::getCollectionProducts();
        $breadcrumb     = \App\Helpers\Url::buildBreadcrumb('gio-hang');
        return view('wallpaper.cart.index', compact('item', 'language', 'breadcrumb', 'products'));
    }

    public static function enIndex(Request $request){
        $language       = 'en';
        SettingController::settingLanguage($language);
        $item           = Page::select('*')
                            ->whereHas('en_seo', function($query){
                                $query->where('slug', 'cart');
                            })
                            ->with('seo', 'en_seo', 'type')
                            ->first();
        $products       = \App\Http\Controllers\CartController::getCollectionProducts();
        $productsCart   = [];
        if(!empty(Cookie::get('cart'))) $productsCart = json_decode(Cookie::get('cart'), true);
        $breadcrumb     = \App\Helpers\Url::buildBreadcrumb('cart', $language);
        return view('wallpaper.cart.index', compact('item', 'language', 'breadcrumb', 'products', 'productsCart'));
    }

    public static function addToCart(Request $request){
        $result         = '';
        $idProduct      = $request->get('product_info_id') ?? 0;
        $arrayPrice     = explode('-', $request->get('product_price_id'));
        /* xây dựng mảng sản phẩm vừa thêm vào giỏ hàng */
        $infoProductInCart = [];
        $infoProductInCart['product_info_id']   = $idProduct;
        $infoProductInCart['product_price_id']  = $arrayPrice;
        /* lấy thông tin */
        $infoProduct    = Product::select('*')
                                ->where('id', $idProduct)
                                ->with('prices', function($query) use($arrayPrice){
                                    $query->whereIn('id', $arrayPrice);
                                })
                                ->first();
        /* tồn tại sản phẩm mới xử lý tiếp */
        if(!empty($infoProduct)){
            /* cart cũ */
            $arrayCart      = Cookie::get('cart');
            $arrayCart      = json_decode($arrayCart, true);
            if(!empty($arrayCart)){
                $flagExist      = false;
                for($i=0;$i<count($arrayCart);++$i){
                    if($idProduct==$arrayCart[$i]['product_info_id']){
                        /* đã tồn tại */
                        $arrayCart[$i]  = $infoProductInCart;
                        $flagExist      = true;
                        break;
                    }
                }
                /* nếu chưa tồn tại */
                if($flagExist==false){
                    $next               = count($arrayCart);
                    $arrayCart[$next]   = $infoProductInCart;
                }
            }else {
                $arrayCart[0]           = $infoProductInCart;
            }
            /* set cookie */
            CookieController::setCookie('cart', json_encode($arrayCart), 3600);
            /* trả thông báo */
            $language       = $request->get('language') ?? 'vi';
            $cartToView     = self::convertInfoCartToView($infoProductInCart, $infoProduct, $language);
            $result = view('wallpaper.cart.cartMessage', [
                'title'     => $infoProduct->name,
                'option'    => $cartToView['option_name'],
                'price'     => $cartToView['price'],
                'image'     => config('main.google_cloud_storage.default_domain').$cartToView['image'],
                'language'  => $language
            ])->render();
        }
        echo $result;
    }

    public static function convertInfoCartToView($productInCart, $infoProduct, $language){
        $result             = [];
        if(!empty($productInCart)&&!empty($infoProduct)){
            if(count($productInCart['product_price_id'])>1){
                $result['option_name']  = $language=='vi' ? 'Trọn bộ' : 'Full set';
                $result['price']        = $infoProduct->price;
            }else {
                $result['option_name']  = $language=='vi' ? $infoProduct->prices[0]->name : $infoProduct->prices[0]->en_name;
                $result['price']        = $infoProduct->prices[0]->price;
            }
            $result['image']            = $infoProduct->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper;
        }
        return $result;
    }

    public static function viewSortCart(Request $request){
        $language = Cookie::get('language') ?? 'vi';
        $products = self::getCollectionProducts();
        $response = view('wallpaper.cart.cartSort', compact('products', 'language'))->render();
        echo $response;
    }

    // public static function updateCart(Request $request){
    //     /* lấy dữ liệu cookie của products */
    //     $tmp            = Cookie::get('cart');
    //     if(!empty($tmp)) $tmp = json_decode($tmp, true);
    //     /* cập nhật lại số lượng sản phẩm */
    //     $products       = [];
    //     if(!empty($request->get('product_info_id'))&&!empty($request->get('quantity'))){
    //         $count      = 0;
    //         $total      = 0;
    //         $i          = 0;
    //         foreach($tmp as $product){
    //             $products[$i]   = $product;
    //             if($product['product_info_id']==$request->get('product_info_id')&&$product['product_price_id']==$request->get('product_price_id')){
    //                 $total      += $request->get('quantity')*$products[$i]['price'];
    //                 $count      += $request->get('quantity');
    //                 /* cập nhật lại quantity */
    //                 $products[$i]['quantity']   = $request->get('quantity');
    //                 /* lấy thông tin product để cập nhật lại giao diện */
    //                 $infoProduct = self::getCollectionProduct($products[$i]);
    //             }else {
    //                 $total += $products[$i]['quantity']*$products[$i]['price'];
    //                 $count += $products[$i]['quantity'];
    //             }
    //             ++$i;
    //         }
    //     }
    //     /* set lại cookie */
    //     CookieController::setCookie('cart', json_encode($products), 3600);
    //     /* lấy dữ liệu của cột thay đổi */
    //     $result['total']        = number_format($total).config('main.currency_unit');
    //     $result['count']        = $count;
    //     if($request->get('theme')=='cartSort'){
    //         $result['row']      = view('main.cart.cartSortRow', ['product' => $infoProduct])->render();
    //     }else {
    //         $result['row']      = view('main.cart.cartRow', ['product' => $infoProduct])->render();
    //     }
    //     return json_encode($result);
    // }

    public static function calculatorTotalInCart(){
        $total          = 0;
        $infoProducts   = self::getCollectionProducts();
        foreach($infoProducts as $infoProduct){
            foreach($infoProduct->prices as $price){
                if($price->id==$infoProduct->product_price_id) $total += $infoProduct->quantity*$price->price;
            }
        }
        return $total;
    }

    public static function removeProductCart(Request $request){
        $tmp                    = json_decode(Cookie::get('cart'), true);
        $cartNew                = [];
        foreach($tmp as $product){
            if($product['product_info_id']!=$request->get('product_info_id')) $cartNew[]      = $product;
        }
        /* set lại cookie */
        CookieController::setCookie('cart', json_encode($cartNew), 3600);
        /* trường hợp remove đến khi cart rỗng */
        return true;
    }

    public static function getCollectionProducts(){
        $infoProducts           = new \Illuminate\Database\Eloquent\Collection;
        $products               = Cookie::get('cart');
        if(!empty($products)) {
            $products           = json_decode($products, true);
            $i                  = 0;
            foreach($products as $product) {
                $arrayPrice         = $product['product_price_id'];
                $infoProducts[$i]   = Product::select('*')
                                        ->where('id', $product['product_info_id'])
                                        ->with(['prices' => function ($query) use ($arrayPrice) {
                                            $query->whereIn('id', $arrayPrice);
                                        }, 'seo', 'prices.wallpapers'])
                                        ->first();
                /* ghép cart vào */
                $infoProducts[$i]['cart'] = $product;
                ++$i;
            }
        }
        return $infoProducts;
    }

    public static function loadTotalCart(Request $request){
        $response = null;
        if(!empty($request->get('payment_method_info_id'))){
            $idPaymentMethod = $request->get('payment_method_info_id');
            /* ngôn ngữ */
            $language = session()->get('language') ?? 'vi';
            /* tiêu đề "tổng cộng" */
            $titleTotal = $language=='vi' ? 'Tổng cộng' : 'Total';
            /* sản phẩm trong giỏ hàng */
            $products = \App\Http\Controllers\CartController::getCollectionProducts();
            $total    = 0;
            foreach($products as $product){
                $total += $product->price;
            }
            /* phí thanh toán (nếu có) */
            $paymentMethod  = PaymentMethod::select('*')
                                ->where('id', $idPaymentMethod)
                                ->first();
            $taxNumber      = $paymentMethod->fee ?? 0;
            $taxName        = $language=='vi' ? 'Phí thanh toán' : 'Payment fee';
            $response = view('wallpaper.cart.total', [
                'titleTotal'    => $titleTotal,
                'total'         => $total,
                'language'      => $language,
                'taxName'       => $taxName,
                'taxNumber'     => $taxNumber
            ])->render();
        }
        echo $response;
    }
}
