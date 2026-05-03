<?php
return [
    'tmn_code'    => env('VNPAY_TMN_CODE', 'DEMOV210'),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    'url'         => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url'  => env('VNPAY_RETURN_URL'),
    'ipn_url'     => env('VNPAY_IPN_URL'),
    'version'     => '2.1.0',
    'locale'      => 'vn',
];