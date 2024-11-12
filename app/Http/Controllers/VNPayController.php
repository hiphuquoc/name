<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Order;

class VNPayController extends Controller{

    public static function create($infoOrder){
        $urlRedirect    = null;
        if(!empty($infoOrder)){
            $vnp_Url        = config('payment.vnpay.endpoint');
            $vnp_TmnCode    = config('payment.vnpay.access_key'); //Mã website tại VNPAY 
            $vnp_HashSecret = config('payment.vnpay.secret_key'); //Chuỗi bí mật
            $vnp_Returnurl  = route('main.handlePaymentVNPay', ['code' => $infoOrder->code]);
            $language       = 'vi';
            $currencyCode   = 'VND';
            /* tổng tiền (không có phí thanh toán) */
            $tmp        = \App\Helpers\Number::getPriceByLanguage($infoOrder->total, $language);
            $total      = $tmp['number'] ?? 0;

            if(!empty($total)&&$total>0){
                $vnp_TxnRef     = $infoOrder->code;
                $vnp_OrderInfo  = 'Name.com.vn - Thanh toán đơn hàng '.$infoOrder->code;
                $vnp_OrderType  = 'billpayment';
                $vnp_Amount     = $total * 100; /* bắt buộc phải * 100 để ra số tiền đúng */
                $vnp_Locale     = $language;
                $vnp_IpAddr     = $_SERVER['REMOTE_ADDR'];
                $inputData = array(
                    "vnp_Version"       => "2.1.0",
                    "vnp_TmnCode"       => $vnp_TmnCode,
                    "vnp_Amount"        => $vnp_Amount,
                    "vnp_Command"       => "pay",
                    "vnp_CreateDate"    => date('YmdHis'),
                    "vnp_CurrCode"      => $currencyCode,
                    "vnp_IpAddr"        => $vnp_IpAddr,
                    "vnp_Locale"        => $vnp_Locale,
                    "vnp_OrderInfo"     => $vnp_OrderInfo,
                    "vnp_OrderType"     => $vnp_OrderType,
                    "vnp_ReturnUrl"     => $vnp_Returnurl,
                    "vnp_TxnRef"        => $vnp_TxnRef,
                );
                
                if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                    $inputData['vnp_BankCode'] = $vnp_BankCode;
                }
                if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                    $inputData['vnp_Bill_State'] = $vnp_Bill_State;
                }
                
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                
                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }
                /* trả đường dẫn redirect */
                $urlRedirect = $vnp_Url;
            }
        }
        return $urlRedirect;
    }
}
