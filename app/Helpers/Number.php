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
        $result         = null;
        if (!empty($number)) {
            $exchangeRate = config('main.exchange_rate.usd_to_vnd');
            $result = ceil($number * $exchangeRate / 1000) * 1000; /* làm tròn lên 3 chữ số */
        }
        return $result;
    }

    public static function getFormatPriceByLanguage($number, $language){
        $result         = null;
        if(empty($language)||$language=='vi'){
            $result     = number_format(self::convertUSDToVND($number)).config('main.currency_unit');
        }else {
            $result     = $number.config('main.currency_unit_en');
        }
        return $result;
    }
}