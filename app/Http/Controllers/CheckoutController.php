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
        $productsCart       = Cookie::get('cart');
        if(!empty($productsCart)) $productsCart = json_decode($productsCart, true);
        $products           = new \Illuminate\Database\Eloquent\Collection;
        foreach($productsCart as $product){
            $idPrice        = $product['product_price_id'];
            $tmp            = Product::select('*')
                                ->where('id', $product['product_info_id'])
                                ->with(['prices' => function($query) use($idPrice) {
                                    $query->where('id', $idPrice);
                                }])
                                ->first();
            $products[]     = $tmp;
        }
        $insertOrder        = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($request->all(), 0, $products);
        $insertOrder['payment_type'] = 'payment_cart';
        $idOrder            = Order::insertItem($insertOrder);
        /* tạo order_product cho order_info => do thanh toán ngay nên chỉ có 1 sản phẩm */
        foreach($products as $product){
            OrderProduct::insertItem([
                'order_info_id'     => $idOrder,
                'product_info_id'   => $product->id,
                'product_price_id'  => $product->prices[0]->id ?? 'all',
                'quantity'          => 1,
                'price'             => $product->prices[0]->price ?? $product->price
            ]);
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
        }
        /* trả về đường dẫn để chuyển hướng */
        return redirect($urlRedirect);
    }

    public function paymentNow(Request $request){
        $urlRedirect        = null;
        /* tạo đơn hàng */
        $products           = new \Illuminate\Database\Eloquent\Collection;
        if(!empty($request->get('product_price_id'))){
            $idPrice        = $request->get('product_price_id');
            $tmp            = Product::select('*')
                                ->where('id', $request->get('product_info_id'))
                                ->with(['prices' => function($query) use($idPrice) {
                                    $query->where('id', $idPrice);
                                }])
                                ->first();
            $products[]     = $tmp;
            $insertOrder    = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($request->all(), 0, $products);
            $insertOrder['payment_type'] = 'payment_now';
            $idOrder        = Order::insertItem($insertOrder);
            /* tạo order_product cho order_info => do thanh toán ngay nên chỉ có 1 sản phẩm */
            OrderProduct::insertItem([
                'order_info_id'     => $idOrder,
                'product_info_id'   => $request->get('product_info_id'),
                'product_price_id'  => $request->get('product_price_id'),
                'quantity'          => 1,
                'price'             => $tmp->prices[0]->price ?? $tmp->price
            ]);
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
                /* tạo yêu cầu thanh toán => nếu momo (ghi chú: ở momo sẽ redirect thẳng) */
                if($orderInfo->paymentMethod->code=='paypal') $urlRedirect = \App\Http\Controllers\PaypalController::create($orderInfo);
            }
            /* trả về đường dẫn để chuyển hướng */
            $reponse['url'] = $urlRedirect;
            return json_encode($reponse);
        }
    }

}
