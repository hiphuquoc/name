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

    public static function getFormatPriceByLanguage($number, $language, $showCurrency = true){
        $result         = null;
        $tmp            = self::getPriceByLanguage($number, $language);
        if($showCurrency==true){
            if($language=='vi'){
                $result     = number_format($tmp['number']).$tmp['currency_code'];
            }else {
                $result     = $tmp['number'].$tmp['currency_code'];
            }
        }else {
            if($language=='vi'){
                $result     = number_format($tmp['number']);
            }else {
                $result     = $tmp['number'];
            }
        }
        return $result;
    }

    public static function getPriceByLanguage($number, $language){
        $result             = [
            'number'    => 0,
            'currency'  => null,
            'currency_code' => null,
        ];
        $exchangeRate       = config('language.'.$language.'.money_value');
        $calculator         = $number * $exchangeRate;
        $result['number']   = $calculator;
        $result['currency']     = config('language.'.$language.'.currency');
        $result['currency_code']   = config('language.'.$language.'.currency_code');
        return $result;
    }
}