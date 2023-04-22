<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Order;

class ZalopayController extends Controller{

    public static function create($infoOrder){
        $urlRedirect    = null;
        if(!empty($infoOrder)){
            $endpoint   = "https://sandbox.zalopay.com.vn/v001/tpe/createorder";
            $embeddata  = [
                "merchantinfo" => "embeddata123"
            ];
            /* truyển thông tin sản phẩm trong order vào */
            $dataItem   = [];
            $i          = 0;
            foreach($infoOrder->products as $product){
                $dataItem[$i]['code']       = $product->infoProduct->code ?? null;
                $dataItem[$i]['name']       = $product->infoProduct->name ?? null;
                $dataItem[$i]['option']     = $product->infoPrice->name ?? null;
                $dataItem[$i]['price']      = $product->infoPrice->price ?? null;
                $dataItem[$i]['sale_off']   = $product->infoPrice->sale_off ?? null;
                ++$i;
            }
            /* tổng tiền */
            $total = $infoOrder->total ?? 0;
            if($total>0){
                $order = [
                    "appid"         => config('payment.zalopay.appid'),
                    "apptime"       => round(microtime(true) * 1000), // miliseconds
                    "apptransid"    => date("ymd")."_".$infoOrder->code, // mã giao dich có định dạng yyMMdd_xxxx
                    "appuser"       => config('payment.zalopay.appuser'),
                    "item"          => json_encode($dataItem, JSON_UNESCAPED_UNICODE),
                    "embeddata"     => json_encode($embeddata, JSON_UNESCAPED_UNICODE),
                    "amount"        => $total,
                    "description"   => "ZaloPay Intergration Demo",
                    "bankcode"      => "zalopayapp"
                ];
                // appid|apptransid|appuser|amount|apptime|embeddata|item
                $data = $order["appid"]."|".$order["apptransid"]."|".$order["appuser"]."|".$order["amount"]
                ."|".$order["apptime"]."|".$order["embeddata"]."|".$order["item"];
                $order["mac"] = hash_hmac("sha256", $data, config('payment.zalopay.key1'));
                    
                $context = stream_context_create([
                    "http" => [
                        "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                        "method" => "POST",
                        "content" => http_build_query($order)
                    ]
                ]);
                
                $resp = file_get_contents($endpoint, false, $context);
                $result = json_decode($resp, true);

                if(!empty($result['orderurl'])) $urlRedirect = $result['orderurl'];
                //   foreach ($result as $key => $value) {
                //     echo "$key: $value<br>";
                //   }
            }
        }
        return $urlRedirect;
    }

}
