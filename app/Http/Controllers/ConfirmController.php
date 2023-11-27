<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Jobs\SendEmailOrder;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ConfirmController extends Controller {

    public static function confirm(Request $request){
        $item       = new \Illuminate\Database\Eloquent\Collection;
        $code       = $request->get('code') ?? 0;
        // $code       = 'XJ1ZABVIS287W4Y';
        $order      = Order::select('*')
                        ->where('code', $code)
                        ->with('products.infoPrice', 'products.infoProduct')
                        ->first();
        $language   = session('language') ?? 'vi';
        if(!empty($order)&&$order->payment_status==1){
            return view('wallpaper.confirm.index', compact('item', 'order', 'language'));
        }
        return redirect()->route('main.home');
    }

    public static function handlePaymentMomo(Request $request){
        /* lấy thông tin của Order trong CSDL */
        $code           = $request->get('orderId');
        $orderInfo      = Order::select('*')
                            ->where('code', $code)
                            ->first();
        $flagPayment    = false;
        /* Trường hợp MOMO: có mã đơn hàng => xử lý tiếp */
        if(!empty($request->get('orderId'))&&$orderInfo->paymentMethod->code=='momo'){
            /* cập nhật trans_id (id thanh toán của momo) */
            $transId    = $request->get('transId') ?? '';
            if(!empty($transId)) Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
            /* kiểm tra đã thanh toán chưa */
            $resultCode = $request->get('resultCode');
            /* nếu đã thanh toán thành công */
            if(in_array($resultCode, config('payment.momo.payment_success_code'))) $flagPayment = true;
        }
        /* xử lý sau khi đã thanh toán thành công */
        if($flagPayment==true) {
            self::handleAfterPayment($orderInfo);
            /* chuyển hướng sang trang nhận ảnh */
            return redirect()->route('main.confirm', ['code' => $code]);
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }  

    public static function handlePaymentZalopay(Request $request){
        if(!empty($request->get('code'))){
            /* lấy thông tin của Order trong CSDL */
            $code           = $request->get('code');
            $orderInfo      = Order::select('*')
                                ->where('code', $code)
                                ->first();
            $flagPayment    = false;
            /* cập nhật trans_id (id thanh toán của zalopay) */
            if(!empty($request->get('apptransid'))&&!empty($request->get('amount'))){ /* tồn tại transid và đã thanh toán > 0 => xử lý tiếp */
                $transId        = $request->get('apptransid');
                Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
                /* nếu đã thanh toán thành công */
                if(!empty($request->get('status'))&&$request->get('status')==1&&$request->get('amount')==$orderInfo->total) $flagPayment = true;
                /* xử lý sau khi đã thanh toán thành công */
                if($flagPayment==true) {
                    self::handleAfterPayment($orderInfo);
                    /* chuyển hướng sang trang nhận ảnh */
                    return redirect()->route('main.confirm', ['code' => $code]);
                }
            }
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }

    public static function handlePaymentPaypal(Request $request){
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);
        if (!empty($response['status'])&&$response['status']=='COMPLETED') {
             /* lấy thông tin của Order trong CSDL */
            $code           = $request->get('code');
            $orderInfo      = Order::select('*')
                                ->where('code', $code)
                                ->first();
            $flagPayment    = false;
            /* cập nhật trans_id (id thanh toán của zalopay) */
            $transId        = $request->get('PayerID');
            Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
            /* nếu đã thanh toán thành công */
            $flagPayment = true;
            /* xử lý sau khi đã thanh toán thành công */
            if($flagPayment==true) {
                self::handleAfterPayment($orderInfo);
                /* chuyển hướng sang trang nhận ảnh */
                return redirect()->route('main.confirm', ['code' => $code]);
            }
        }
        /* thanh toán không thành công */
        return redirect()->route('main.home');
    }
    
    private static function handleAfterPayment($orderInfo){
        if(!empty($orderInfo)){
            /* cập nhật trạng thái thành toán thành công */
            Order::updateItem($orderInfo->id, ['payment_status' => 1]);
            /* nếu là thanh toán giỏ hàng => clear giỏ hàng */
            if($orderInfo->payment_type=='payment_cart') \App\Http\Controllers\CartController::removeCookie('cart');
            /* tạo job gửi email */
            if(!empty($orderInfo->email)) SendEmailOrder::dispatch($orderInfo);
        }
    }
}
