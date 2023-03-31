<?php 


return [
    'momo'      => [
        'action'            => 'Thanh toán qua Momo',
        'partner_name'      => 'Name.com.vn',
        'endpoint_create'   => 'https://test-payment.momo.vn/v2/gateway/api/create',
        'store_id'          => 'MomoTestStore',
        'partner_code'      => 'MOMOBKUN20180529',
        'access_key'        => 'klm05TvNBzhg7h7j',
        'secret_key'        => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'
    ],
    'zalopay'   => [
        'appid' => '553',
        'appuser' => 'demo',
        "key1"  => "9phuAOYhan4urywHTh0ndEXiV3pKHr5Q",
        "key2"  => "Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3",
        "api"   => [
            "createorder"       => "https  =>//sandbox.zalopay.com.vn/v001/tpe/createorder",
            "gateway"           => "https  =>//sbgateway.zalopay.vn/pay?order=",
            "quickpay"          => "https  =>//sandbox.zalopay.com.vn/v001/tpe/submitqrcodepay",
            "refund"            => "https  =>//sandbox.zalopay.com.vn/v001/tpe/partialrefund",
            "getrefundstatus"   => "https  =>//sandbox.zalopay.com.vn/v001/tpe/getpartialrefundstatus",
            "getorderstatus"    => "https  =>//sandbox.zalopay.com.vn/v001/tpe/getstatusbyapptransid",
            "getbanklist"       => "https  =>//sbgateway.zalopay.vn/api/getlistmerchantbanks"
        ],
        // "db"    => [
        //     "host"      => "mysql",
        //     "port"      => 3306,
        //     "dbname"    => "zalopay_demo",
        //     "user"      => "root",
        //     "password"  => "123456"
        // ]
    ]
];