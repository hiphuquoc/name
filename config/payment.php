<?php 


return [
    'momo'      => [
        'action'            => 'Thanh toán qua Momo',
        'partner_name'      => 'Name.com.vn',
        'endpoint_create'   => 'https://test-payment.momo.vn/v2/gateway/api/create',
        'store_id'          => 'MomoTestStore',
        'partner_code'      => 'MOMOLNCV20230412',
        'access_key'        => 'p05z68oqDjIh0rF3',
        'secret_key'        => 'd2KCebzFK9DPONFVDiAJMAgeY88B7if9',
        'payment_success_code'  => [
            0, 9000
        ]
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