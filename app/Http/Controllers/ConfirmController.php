<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\SourceFile;

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
        // if(!empty($order)){
        //     return view('wallpaper.confirm.index', compact('item', 'order'));
        // }
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
            if(in_array($resultCode, config('payment.momo.payment_success_code'))){
                /* đã thanh toán thành công */
                /* cập nhật trạng thái thành toán thành công */
                Order::updateItem($orderInfo->id, [
                    'payment_status' => 1
                ]);
                /* nếu là thanh toán giỏ hàng => clear giỏ hàng */
                if($orderInfo->payment_type=='payment_cart') \App\Http\Controllers\CartController::removeCookie('cart');
                /* chuyển hướng sang trang nhận ảnh */
                return redirect()->route('main.confirm', ['code' => $code]);
            }else {
                /* thanh toán không thành công */
                return redirect()->route('main.home');
            }
        }
    }

    public function downloadSource(Request $request){
        $fullPath       = '';
        $fileName       = '';
        if(!empty($request->get('source_info_id'))){
            $infoSource = SourceFile::select('*')
                            ->where('id', $request->get('source_info_id'))
                            ->first();
            $fullPath   = Storage::disk('google')->url($infoSource->file_path);
            $fileName   = $infoSource->file_name;
        }
        $result['url']      = $fullPath;
        $result['filename'] = $fileName;
        return json_encode($result);
    }

    public function downloadSourceAll(Request $request){
        $urls           = [];
        if(!empty($request->get('code'))){
            $order      = Order::select('*')
                            ->where('code', $request->get('code'))
                            ->with('products')
                            ->first();
            foreach($order->products as $product){
                foreach($product->infoPrice->sources as $source){
                    $urls[] = env('APP_URL').Storage::url($source->file_path);
                }
            }
        }
        return json_encode($urls);
    }
    
}
