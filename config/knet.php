<?php

return [
    'merchant_id' => env('KNET_MERCHANT_ID', ''),
    'merchant_password' => env('KNET_MERCHANT_PASSWORD', ''),
    'encrypted_key' => env('KNET_ENCRYPTED_KEY', ''),
    'return_url' => env('KNET_RETURN_URL', ''),
    'server_ip_1' => env('KNET_SERVER_IP_1', ''),
    'server_ip_2' => env('KNET_SERVER_IP_2', ''),
    'gateway_url' => env('KNET_GATEWAY_URL', 'https://kpaytest.com.kw/kpg/PaymentHTTP.htm'),
    'is_sandbox' => env('KNET_SANDBOX', true),
    'currency' => env('KNET_CURRENCY', '414'), // KWD currency code
    'language' => env('KNET_LANGUAGE', 'ENG'), // ENG or ARA
];
