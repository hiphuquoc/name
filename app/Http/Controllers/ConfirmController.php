<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Order;
use App\Jobs\SendEmailOrder;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ConfirmController extends Controller {

    public static function confirm(Request $request, $slug){
        $language   = SettingController::getLanguageBySlug($slug);
        SettingController::settingLanguage($language);
        $item       = Page::select("*")
                        ->whereHas('seos.infoSeo', function($query) use($slug){
                            $query->where('slug', $slug);
                        })
                        ->first();
        /* lấy item seo theo ngôn ngữ được chọn */
        $itemSeo            = [];
        if (!empty($item->seos)) {
            foreach ($item->seos as $s) {
                if ($s->infoSeo->language == $language) {
                    $itemSeo = $s->infoSeo;
                    break;
                }
            }
        }
        $code       = $request->get('code') ?? 0;
        // $code       = 'FU3J5S0NLRCVAD2';
        $order      = Order::select('*')
                        ->where('code', $code)
                        ->with('products.infoPrice', 'products.infoProduct')
                        ->first();
        $language   = session('language') ?? 'vi';
        if(!empty($order)&&$order->payment_status==1){
            return view('wallpaper.confirm.index', compact('item', 'itemSeo', 'order', 'language'));
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
            /* lấy slug theo ngôn ngữ của trang xác nhận */
            $language   = SettingController::getLanguage();
            $slug       = self::getSlugPageConfirmByLanguage($language);
            /* chuyển hướng sang trang nhận ảnh */
            if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
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
                if(!empty($request->get('status'))&&$request->get('status')==1) $flagPayment = true;
                /* xử lý sau khi đã thanh toán thành công */
                if($flagPayment==true) {
                    self::handleAfterPayment($orderInfo);
                    /* lấy slug theo ngôn ngữ của trang xác nhận */
                    $language   = SettingController::getLanguage();
                    $slug       = self::getSlugPageConfirmByLanguage($language);
                    /* chuyển hướng sang trang nhận ảnh */
                    if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
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
                /* lấy slug theo ngôn ngữ của trang xác nhận */
                $language   = SettingController::getLanguage();
                $slug       = self::getSlugPageConfirmByLanguage($language);
                /* chuyển hướng sang trang nhận ảnh */
                if(!empty($slug)) return redirect()->route('main.confirm', ['slug' => $slug, 'code' => $code]);
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

    private static function getSlugPageConfirmByLanguage($language){
        $slug       = '';
        if(!empty($language)){
            $tmp        = Page::select('*')
                            ->whereHas('seos.infoSeo', function($query){
                                $query->whereIn('slug', config('main.url_confirm_page'));
                            })
                            ->first();
            
            foreach($tmp->seos as $seo){
                if($seo->infoSeo->language==$language) {
                    $slug = $seo->infoSeo->slug;
                    break;
                }
            }
        }
        return $slug;
    }
}
