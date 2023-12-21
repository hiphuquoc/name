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
        $productsCart   = [];
        if(!empty(Cookie::get('cart'))) $productsCart = json_decode(Cookie::get('cart'), true);
        $breadcrumb     = \App\Helpers\Url::buildBreadcrumb('gio-hang');
        return view('wallpaper.cart.index', compact('item', 'language', 'breadcrumb', 'products', 'productsCart'));
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
        /* cart cũ */
        $cartOld        = Cookie::get('cart');
        if(!empty($cartOld)){
            /* trong giỏ hàng đã có sản phẩm */
            $tmp        = json_decode($cartOld, true);
            $flagMatch  = false;
            $cartNew    = [];
            $i          = 0;
            for($i=0;$i<count($tmp);++$i){
                /* sản phẩm không trùng thì thêm vào mảng mới => trùng thì xử lý bước sau */
                if($tmp[$i]['product_info_id']==$request->get('product_info_id')){
                    $cartNew[]      = [
                        'product_info_id'   => $request->get('product_info_id'),
                        'product_price_id'  => $request->get('product_price_id'),
                        'price'             => $request->get('price')
                    ];
                    $flagMatch      = true;
                }else {
                    $cartNew[]      = $tmp[$i];
                }
            }
            /* trường hợp sản phẩm chưa có trong giỏ hàng => thêm vào mảng */
            if($flagMatch==false) {
                $cartNew[count($cartNew)] = [
                    'product_info_id'   => $request->get('product_info_id'),
                    'product_price_id'  => $request->get('product_price_id'),
                    'price'             => $request->get('price')
                ];
            }
        }else {
            /* trong giỏ hàng chưa có sản phẩm */
            $cartNew    = [
                [
                    'product_info_id'   => $request->get('product_info_id'),
                    'product_price_id'  => $request->get('product_price_id'),
                    /* ghi cookie thêm price để dễ tính total lúc update cart */
                    'price'             => $request->get('price')
                ]
            ];
        }
        /* set cookie */
        CookieController::setCookie('cart', json_encode($cartNew), 3600);
        /* trả thông báo */
        $language       = $request->get('language') ?? 'vi';
        $result = view('wallpaper.cart.cartMessage', [
            'title'     => $request->get('title') ?? null,
            'option'    => $request->get('option_name') ?? null,
            'price'     => $request->get('price') ?? null,
            'image'     => $request->get('image') ?? null,
            'language'  => $language
        ]);
        echo $result;
    }

    public static function viewSortCart(Request $request){
        $language = $request->get('language') ?? 'vi';
        $products = self::getCollectionProducts();
        $response = view('wallpaper.cart.cartSort', compact('products', 'language'))->render();
        echo $response;
    }

    public static function updateCart(Request $request){
        /* lấy dữ liệu cookie của products */
        $tmp            = Cookie::get('cart');
        if(!empty($tmp)) $tmp = json_decode($tmp, true);
        /* cập nhật lại số lượng sản phẩm */
        $products       = [];
        if(!empty($request->get('product_info_id'))&&!empty($request->get('quantity'))){
            $count      = 0;
            $total      = 0;
            $i          = 0;
            foreach($tmp as $product){
                $products[$i]   = $product;
                if($product['product_info_id']==$request->get('product_info_id')&&$product['product_price_id']==$request->get('product_price_id')){
                    $total      += $request->get('quantity')*$products[$i]['price'];
                    $count      += $request->get('quantity');
                    /* cập nhật lại quantity */
                    $products[$i]['quantity']   = $request->get('quantity');
                    /* lấy thông tin product để cập nhật lại giao diện */
                    $infoProduct = self::getCollectionProduct($products[$i]);
                }else {
                    $total += $products[$i]['quantity']*$products[$i]['price'];
                    $count += $products[$i]['quantity'];
                }
                ++$i;
            }
        }
        /* set lại cookie */
        CookieController::setCookie('cart', json_encode($products), 3600);
        /* lấy dữ liệu của cột thay đổi */
        $result['total']        = number_format($total).config('main.currency_unit');
        $result['count']        = $count;
        if($request->get('theme')=='cartSort'){
            $result['row']      = view('main.cart.cartSortRow', ['product' => $infoProduct])->render();
        }else {
            $result['row']      = view('main.cart.cartRow', ['product' => $infoProduct])->render();
        }
        return json_encode($result);
    }

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
        $tmp                    = Cookie::get('cart');
        if(!empty($tmp)) $tmp = json_decode($tmp, true);
        /* xóa product và tính lại total + count */
        $products               = [];
        $total                  = 0;
        $count                  = 0;
        $i                      = 0;
        foreach($tmp as $product){
            if($product['product_info_id']==$request->get('product_info_id')){
                /* không làm gì cả */
            }else {
                $products[$i]   = $product;
                $total          += $product['price'];
                $count          += 1;
                ++$i;
            }
        }
        /* set lại cookie */
        CookieController::setCookie('cart', json_encode($products), 3600);
        $language               = session('language') ?? 'vi';
        $result['total']        = \App\Helpers\Number::getFormatPriceByLanguage($total, $language);
        $result['count']        = $count;
        /* trường hợp remove đến khi cart rỗng */
        $result['empty_cart']   = '';
        if($total==0) $result['empty_cart'] = view('wallpaper.cart.emptyCart')->render();
        return json_encode($result);
    }

    public static function getCollectionProducts(){
        $products           = Cookie::get('cart');
        if(!empty($products)) $products = json_decode($products, true);
        /* duyệt từ từ qua mảng để lấy lần lượt product ứng với price */
        $infoProducts       = new \Illuminate\Database\Eloquent\Collection;
        if(!empty($products)){
            foreach($products as $product) {
                $infoProducts[] = self::getCollectionProduct($product);
            }
        }
        return $infoProducts;
    }

    public static function getCollectionProduct($productInCart){
        $idPrice    = $productInCart['product_price_id'];
        if($idPrice!='all'){
            $tmp    = Product::select('*')
                        ->whereHas('prices', function($query) use($idPrice){
                            $query->where('id', $idPrice);
                        })
                        ->with(['prices' => function ($query) use ($idPrice) {
                            $query->where('id', $idPrice);
                        }, 'seo', 'prices.wallpapers'])
                        ->first();
        }else {
            $idProduct  = $productInCart['product_info_id'];
            $tmp        = Product::select('*')
                            ->where('id', $idProduct)
                            ->with('seo')
                            ->first();
        }
        if(!empty($tmp)){
            /* ghép product_price được chọn vào collection */
            foreach($tmp->prices as $price){
                if($idPrice==$price->id) {
                    $tmp->price = $price;
                    break;
                }
            }
            /* ghép cookie vào collection */
            $tmp->cart      = collect($productInCart);
        }
        /* đưa phần tử collection vào collection cha */
        return $tmp;
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
