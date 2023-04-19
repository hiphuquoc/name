<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\GoogledriveController;
use App\Models\Order;
use App\Models\SourceFile;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use App\Jobs\SendEmailOrder;

class ConfirmController extends Controller {

    public static function confirm(Request $request){
        $item       = new \Illuminate\Database\Eloquent\Collection;
        $code       = $request->get('code') ?? 0;
        $order      = Order::select('*')
                        ->where('code', $code)
                        ->with('products')
                        ->first();
        if(!empty($order)&&$order->payment_status==1){
            return view('wallpaper.confirm.index', compact('item', 'order'));
        }
        return redirect()->route('main.home');
    }

    public static function handlePaymentMomo(Request $request){
        /* có mã đơn hàng => xử lý tiếp */
        if(!empty($request->get('orderId'))&&!empty($request->get('transId'))){
            $code       = $request->get('orderId');
            $orderInfo  = Order::select('*')
                            ->where('code', $code)
                            ->first();
            /* cập nhật trans_id (id thanh toán của momo) */
            $transId    = $request->get('transId');
            Order::updateItem($orderInfo->id, [
                'trans_id' => $transId
            ]);
            /* kiểm tra đã thanh toán chưa */
            $resultCode = $request->get('resultCode');
            if(in_array($resultCode, config('payment.momo.payment_success_code'))){ /* đã thanh toán thành công */
                /* cập nhật trạng thái thành toán thành công */
                Order::updateItem($orderInfo->id, [
                    'payment_status' => 1
                ]);
                /* nếu là thanh toán giỏ hàng => clear giỏ hàng */
                if($orderInfo->payment_type=='payment_cart') \App\Http\Controllers\CartController::removeCookie('cart');
                /* tạo job gửi email */
                if(!empty($orderInfo->email)) SendEmailOrder::dispatch($orderInfo);
                /* chuyển hướng sang trang nhận ảnh */
                return redirect()->route('main.confirm', ['code' => $code]);
            }else {
                /* thanh toán không thành công */
                return redirect()->route('main.home');
            }
        }
    }    
}
