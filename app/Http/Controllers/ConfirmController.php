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

    public static function handlePayment(Request $request){
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
        /* Trường hợp Zalopay: */
        if(!empty($request->get('data'))&&$orderInfo->paymentMethod->code=='zalopay'){
            /* cập nhật trans_id (id thanh toán của momo) */
            $transId    = $request->get('data')['zp_trans_id'];
            Order::updateItem($orderInfo->id, ['trans_id' => $transId]);
            /* nếu có request chắc chắn đã thanh toán thành công */
            dd($request->all());
            // $flagPayment = true;
        }
        /* xử lý sau khi đã thanh toán */
        if($flagPayment==true) {
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
