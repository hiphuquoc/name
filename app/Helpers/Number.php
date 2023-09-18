<?php

namespace App\Helpers;

class Number {

    public static function calculatorPriceBeforeSaleoff($price, $saleOff){
        $result         = 0;
        if(!empty($price)&&!empty($saleOff)){
            $result     = ($price*100)/(100 - $saleOff);
        }
        return $result;
    }

    public static function convertUSDToVND($number){
        $result     = null;
        if(!empty($number)) $result = $number*config('main.exchange_rate.usd_to_vnd');
        return $result;
    }
}