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

    public function paymentNow(Request $request){
        $urlRedirect    = null;
        /* tạo đơn hàng */
        $products       = new \Illuminate\Database\Eloquent\Collection;
        $idPrice        = $request->get('product_price_id');
        $tmp            = Product::select('*')
                            ->where('id', $request->get('product_info_id'))
                            ->with(['prices' => function($query) use($idPrice) {
                                $query->where('id', $idPrice);
                            }])
                            ->first();
        $products[]     = $tmp;
        $insertOrder    = $this->BuildInsertUpdateModel->buildArrayTableOrderInfo($request->all(), 0, $products);
        $idOrder        = Order::insertItem($insertOrder);
        /* tạo order_product cho order_info => do thanh toán ngay nên chỉ có 1 sản phẩm */
        OrderProduct::insertItem([
            'order_info_id'     => $idOrder,
            'product_info_id'   => $request->get('product_info_id'),
            'product_price_id'  => $request->get('product_price_id'),
            'quantity'          => 1,
            'price'             => $tmp->prices[0]->price
        ]);
        /* lấy ngược lại thông tin order để xử lý cho chính xác */
        $infoOrder      = Order::select('*')
                            ->where('id', $idOrder)
                            ->with('products.infoProduct', 'products.infoPrice', 'paymentMethod')
                            ->first();
        if(!empty($infoOrder->paymentMethod->code)){
            /* tạo yêu cầu thanh toán => nếu zalo pay */
            if($infoOrder->paymentMethod->code=='zalopay') $urlRedirect = \App\Http\Controllers\ZalopayController::create($infoOrder);
            /* tạo yêu cầu thanh toán => nếu momo */
            if($infoOrder->paymentMethod->code=='momo') $urlRedirect = \App\Http\Controllers\MomoController::create($infoOrder);
        }
        /* trả về đường dẫn để chuyển hướng */
        $reponse['url'] = $urlRedirect;
        return json_encode($reponse);
    }

}
