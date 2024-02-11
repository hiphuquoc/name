<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Services\BuildInsertUpdateModel;

class CheckoutController extends Controller{

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public static function index(Request $request){
        $payments       = PaymentMethod::all();
        // $provinces      = Province::all();
        $products       = \App\Http\Controllers\CartController::getCollectionProducts();
        $productsCart   = json_decode(Cookie::get('cart'), true);
        return view('main.checkout.index', compact('payments', 'provinces', 'products', 'productsCart'));
    }

    public function paymentCart(Request $request){
        $urlRedirect        = null;
        /* tạo đơn hàng */
        $productsCart       = session()->get('cart');
        // $products           = new \Illuminate\Database\Eloquent\Collection;
        // foreach($productsCart as $product){
        //     $idPrice        = $product['product_price_id'];
        //     $tmp            = Product::select('*')
        //                         ->where('id', $product['product_info_id'])
        //                         ->with(['prices' => function($query) use($idPrice) {
        //                             $query->where('id', $idPrice);
        //                         }])
        //                         ->first();
        //     $products[]     = $tmp;
        // }
        if(!empty($productsCart)&&!empty($request->get('payment_method_info_id'))){
            $productsCart       = json_decode($productsCart, true);
            $language           = session()->get('language') ?? 'vi';
            $idPaymentMethod    = $request->get('payment_method_info_id');
            $detailCart         = \App\Http\Controllers\CartController::calculatorDetailCart($productsCart, $idPaymentMethod, $language);
            $insertOrder        = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($request->all(), 0, $detailCart);
            $insertOrder['payment_type'] = 'payment_cart';
            $idOrder            = Order::insertItem($insertOrder);
            /* tạo order_product cho order_info */
            foreach($productsCart as $product){
                foreach($product['product_price_id'] as $price){
                    OrderProduct::insertItem([
                        'order_info_id'     => $idOrder,
                        'product_info_id'   => $product['product_info_id'],
                        'product_price_id'  => $price,
                        'quantity'          => 1,
                        'price'             => 0
                    ]);
                }
            }
            /* lấy ngược lại thông tin order để xử lý cho chính xác */
            $orderInfo      = Order::select('*')
                                ->where('id', $idOrder)
                                ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                                ->first();
            if(!empty($orderInfo->paymentMethod->code)){
                /* tạo yêu cầu thanh toán => nếu zalo pay */
                if($orderInfo->paymentMethod->code=='zalopay') $urlRedirect = \App\Http\Controllers\ZalopayController::create($orderInfo);
                /* tạo yêu cầu thanh toán => nếu momo */
                if($orderInfo->paymentMethod->code=='momo') $urlRedirect = \App\Http\Controllers\MomoController::create($orderInfo);
                /* tạo yêu cầu thanh toán => nếu paypal */
                if($orderInfo->paymentMethod->code=='paypal') $urlRedirect = \App\Http\Controllers\PaypalController::create($orderInfo);
            }
        }
        /* trả về đường dẫn để chuyển hướng */
        return redirect($urlRedirect);
    }

    public function paymentNow(Request $request){
        $urlRedirect        = null;
        /* tạo đơn hàng */
        if(!empty($request->get('product_price_id'))){
            $arrayPrice     = explode('-', $request->get('product_price_id'));
            $idProduct      = $request->get('product_info_id');
            $language           = session()->get('language') ?? 'vi';
            $idPaymentMethod    = $request->get('payment_method_info_id');
            /* giả lập cart */
            $cart           = [
                [
                    'product_info_id' => $idProduct,
                    'product_price_id'  => $arrayPrice
                ]
            ];
            $detailCart     = \App\Http\Controllers\CartController::calculatorDetailCart($cart, $idPaymentMethod, $language);
            $insertOrder    = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($request->all(), 0, $detailCart);
            $insertOrder['payment_type'] = 'payment_now';
            $idOrder        = Order::insertItem($insertOrder);
            /* tạo order_product cho order_info */
            foreach($cart as $product){
                foreach($product['product_price_id'] as $price){
                    OrderProduct::insertItem([
                        'order_info_id'     => $idOrder,
                        'product_info_id'   => $product['product_info_id'],
                        'product_price_id'  => $price,
                        'quantity'          => 1,
                        'price'             => 0
                    ]);
                }
            }
            /* lấy ngược lại thông tin order để xử lý cho chính xác */
            $orderInfo      = Order::select('*')
                                ->where('id', $idOrder)
                                ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                                ->first();
            if(!empty($orderInfo->paymentMethod->code)){
                /* tạo yêu cầu thanh toán => nếu zalo pay */
                if($orderInfo->paymentMethod->code=='zalopay') $urlRedirect = \App\Http\Controllers\ZalopayController::create($orderInfo);
                /* tạo yêu cầu thanh toán => nếu momo (ghi chú: ở momo sẽ redirect thẳng) */
                if($orderInfo->paymentMethod->code=='momo') $urlRedirect = \App\Http\Controllers\MomoController::create($orderInfo);
                /* tạo yêu cầu thanh toán => nếu paypal */
                if($orderInfo->paymentMethod->code=='paypal') $urlRedirect = \App\Http\Controllers\PaypalController::create($orderInfo);
            }
            /* trả về đường dẫn để chuyển hướng */
            $reponse['url'] = $urlRedirect;
            return json_encode($reponse);
        }
    }

}
