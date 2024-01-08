<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CookieController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use App\Models\Page;
use App\Models\Product;
use App\Models\PaymentMethod;

class CartController extends Controller{

    public static function index(Request $request){
        $currentRoute   = Route::currentRouteName();
        $language       = $currentRoute=='main.cart' ? 'vi' : 'en';
        SettingController::settingLanguage($language);
        $item           = Page::select('*')
                            ->when($currentRoute=='main.cart', function($query){
                                $query->whereHas('seo', function($query) {
                                    $query->where('slug', 'gio-hang');
                                });
                            })
                            ->when($currentRoute=='main.enCart', function($query){
                                $query->whereHas('en_seo', function($query) {
                                    $query->where('slug', 'cart');
                                });
                            })
                            ->with('seo', 'en_seo', 'type')
                            ->first();
        $products       = \App\Http\Controllers\CartController::getCollectionProducts();
        $productsCart   = json_decode(session()->get('cart'), true);
        $detailCart     = self::calculatorDetailCart($productsCart, 0, $language);
        $breadcrumb     = \App\Helpers\Url::buildBreadcrumb('gio-hang');
        return view('wallpaper.cart.index', compact('item', 'language', 'breadcrumb', 'products', 'detailCart'));
    }

    public static function addToCart(Request $request){
        $result         = '';
        $idProduct      = $request->get('product_info_id') ?? 0;
        $arrayPrice     = explode('-', $request->get('product_price_id'));
        $type           = $request->get('type');
        /* xây dựng mảng sản phẩm vừa thêm vào giỏ hàng */
        $infoProductInCart = [];
        $infoProductInCart['product_info_id']   = $idProduct;
        $infoProductInCart['product_price_id']  = $arrayPrice;
        $infoProductInCart['type']              = $type;
        /* lấy thông tin */
        $infoProduct    = Product::select('*')
                            ->where('id', $idProduct)
                            ->with('prices')
                            ->first();
        /* tồn tại sản phẩm mới xử lý tiếp */
        if(!empty($infoProduct)){
            /* cart cũ */
            $arrayCart      = session()->get('cart');
            $arrayCart      = json_decode($arrayCart, true);
            if(!empty($arrayCart)){
                $flagExist  = false;
                for($i=0;$i<count($arrayCart);++$i){
                    if($idProduct==$arrayCart[$i]['product_info_id']){ /* đã tồn tại */
                        /* so sánh tiếp loại 
                            -> nếu trùng thì gộp mảng price
                            -> nếu khác thì thay thế mảng price
                        */
                        if($type==$arrayCart[$i]['type']){
                            foreach($arrayPrice as $p){
                                if(!in_array($p, $arrayCart[$i]['product_price_id'])) {
                                    $arrayCart[$i]['product_price_id'][]  = $p;
                                }
                            }
                        }else {
                            $arrayCart[$i] = $infoProductInCart;
                        }
                        $flagExist      = true;
                        break;
                    }
                }
                /* nếu chưa tồn tại */
                if($flagExist==false) $arrayCart[] = $infoProductInCart;
            }else {
                $arrayCart[0]   = $infoProductInCart;
            }
            // /* set cookie */
            // CookieController::setCookie('cart', json_encode($arrayCart), 3600);
            /* set session */
            Session::put('cart', json_encode($arrayCart));
            /* trả thông báo */
            $language       = session()->get('language') ?? 'vi';
            $cartToView     = self::convertInfoCartToView($infoProduct, $infoProductInCart['product_price_id'], $language);
            // dd($cartToView);
            $result = view('wallpaper.cart.cartMessage', [
                'title'     => $cartToView['product_name'],
                'option'    => $cartToView['option_name'],
                'price'     => $cartToView['price'],
                'image'     => config('main.google_cloud_storage.default_domain').$cartToView['image'],
                'language'  => $language
            ])->render();
        }
        echo $result;
    }

    public static function convertInfoCartToView($infoProduct, $priceInCart, $language){
        $result             = [];
        if(!empty($priceInCart)&&!empty($infoProduct)){
            if(count($priceInCart)>=$infoProduct->prices->count()){ /* trọn bộ */
                $result['option_name']  = $language=='vi' ? 'Trọn bộ' : 'Full set';
                $result['price']        = $infoProduct->price;
                $result['image']        = $infoProduct->prices[0]->wallpapers[0]->infoWallpaper->file_cloud_wallpaper;
            }else {
                /* trường hợp không phải trọn bộ => xử lý chỉ 1 */
                $idPriceChoose          = $priceInCart[0];
                $infoPriceChoose        = new \Illuminate\Database\Eloquent\Collection;
                foreach($infoProduct->prices as $price){
                    if($price->id==$idPriceChoose){
                        $infoPriceChoose = $price;
                        break;
                    }
                }
                $result['option_name']  = $language=='vi' ? $infoPriceChoose->name : $infoPriceChoose->en_name;
                $result['price']        = $infoPriceChoose->price;
                $result['image']        = $infoPriceChoose->wallpapers[0]->infoWallpaper->file_cloud_wallpaper;
            }
            $result['product_name']     = $language=='vi' ? $infoProduct->name : $infoProduct->en_name;
        }
        return $result;
    }

    public static function viewSortCart(Request $request){
        $language = session()->get('language') ?? 'vi';
        $cart       = session()->get('cart');
        $cart       = json_decode($cart, true);
        $products = self::getCollectionProducts();
        $detailCart = self::calculatorDetailCart($cart, 0, $language);
        $response = view('wallpaper.cart.cartSort', compact('products', 'detailCart', 'language'))->render();
        echo $response;
    }

    // public static function updateCart(Request $request){
    //     /* lấy dữ liệu cookie của products */
    //     $tmp            = session()->get('cart');
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

    public static function removeProductCart(Request $request){
        $tmp                    = json_decode(session()->get('cart'), true);
        $cartNew                = [];
        for($i=0;$i<count($tmp);++$i){
            if(!empty($tmp[$i])){
                if($tmp[$i]['product_info_id']!=$request->get('product_info_id')) {
                    $cartNew[$i]      = $tmp[$i];
                } else {
                    $arrayProductPriceId = json_decode($request->get('product_price_id'), true);
                    $result = array_diff($tmp[$i]['product_price_id'], $arrayProductPriceId);
                    if(!empty($result)) {
                        $cartNew[$i]['product_info_id']     = $tmp[$i]['product_info_id'];
                        $cartNew[$i]['product_price_id']    = $result;
                        $cartNew[$i]['type']                = $tmp[$i]['type'];
                    }
                }
            }
        }
        /* set lại cookie */
        Session::put('cart', json_encode($cartNew));
        /* trường hợp remove đến khi cart rỗng */
        $language               = session()->get('language') ?? 'vi';
        $detailCart             = self::calculatorDetailCart($cartNew, 0, $language);
        $result                 = [];
        $result['count']        = $detailCart['count'];
        $result['total']        = \App\Helpers\Number::getFormatPriceByLanguage($detailCart['total'], $language);
        /* empty cart */
        $result['isEmpty']      = '';
        if(empty($cartNew)){
            $result['isEmpty']  = view('wallpaper.cart.emptyCart', compact('language'))->render();
        }
        return json_encode($result);
    }

    public static function calculatorDetailCart($cart, $idPaymentMethod = 0, $language){
        $result         = [];
        $intoMoney      = 0;
        $count          = 0;
        $infoProducts   = self::getCollectionProducts($cart);
        foreach($infoProducts as $infoProduct){
            if(count($infoProduct->cart['product_price_id'])==$infoProduct->prices->count()){
                /* trường hợp trọn bộ */
                $tmp        = self::convertInfoCartToView($infoProduct, $infoProduct->cart['product_price_id'], $language);
                $intoMoney  += $tmp['price'];
                $count      += 1;
            }else {
                /* trường hợp từng ảnh */
                foreach($infoProduct->cart['product_price_id'] as $productPriceId){
                    $tmp        = self::convertInfoCartToView($infoProduct, [$productPriceId], $language);
                    $intoMoney  += $tmp['price'];
                    $count      += 1;
                }
            }
        }
        $result['count']        = $count;
        /* phí thanh toán */
        $paymentMethod          = PaymentMethod::select('*')
                                    ->where('id', $idPaymentMethod)
                                    ->first();
        $fee                    = $paymentMethod->fee ?? 0;
        $result['fee']          = $fee;
        $result['into_money']   = $intoMoney;
        $result['total']        = $intoMoney + $fee;
        return $result;
    }

    public static function getCollectionProducts($cartNew = null){
        /* biến cartNew dùng để lúc thay đổi cookie chưa cập nhật lại */
        $infoProducts           = new \Illuminate\Database\Eloquent\Collection;
        $products               = empty($cartNew) ? session()->get('cart') : json_encode($cartNew);
        if(!empty($products)) {
            $products           = json_decode($products, true);
            foreach($products as $product) {
                $infoProduct    = Product::select('*')
                                        ->where('id', $product['product_info_id'])
                                        ->first();
                /* ghép cart vào */
                $infoProduct->cart = $product;
                $infoProducts->add($infoProduct);
            }
        }
        return $infoProducts;
    }

    public static function loadTotalCart(Request $request){
        $response   = '';
        /* ngôn ngữ */
        $language   = session()->get('language') ?? 'vi';
        /* tiêu đề "tổng cộng" */
        $titleTotal = $language=='vi' ? 'Tổng cộng' : 'Total';
        /* sản phẩm trong giỏ hàng */
        $cart       = json_decode(session()->get('cart'), true);
        $idPaymentMethod = $request->get('payment_method_info_id') ?? 0;
        $detailCart   = self::calculatorDetailCart($cart, $idPaymentMethod, $language);
        /* phí thanh toán (nếu có) */
        $paymentMethod  = PaymentMethod::select('*')
                            ->where('id', $idPaymentMethod)
                            ->first();
        $taxNumber      = $paymentMethod->fee ?? 0;
        $response = view('wallpaper.cart.total', [
            'intoMoney'     => $detailCart['into_money'],
            'titleTotal'    => $titleTotal,
            'total'         => $detailCart['total'],
            'language'      => $language,
            'taxNumber'     => $taxNumber
        ])->render();
        echo $response;
    }
}
