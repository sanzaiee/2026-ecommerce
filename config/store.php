<?php

return [

    'currency' => 'NPR',

    /*
    |--------------------------------------------------------------------------
    | Storefront theme (defaults when DB values are empty)
    |--------------------------------------------------------------------------
    */
    'theme' => [
        'primary' => env('STORE_THEME_PRIMARY', '#b91c1c'),
        'primary_dark' => env('STORE_THEME_PRIMARY_DARK', '#991b1b'),
        'hero_accent' => env('STORE_THEME_HERO_ACCENT', '#dceee9'),
    ],

    'checkout' => [
        'require_auth' => true,
    ],

    'shipping' => [
        'flat_rate' => (float) env('STORE_SHIPPING_FLAT_RATE', 150),
        'free_threshold' => (float) env('STORE_FREE_SHIPPING_THRESHOLD', 2000),
    ],

    'payments' => [
        /*
        | Active online gateway: esewa | khalti | null (online payments disabled)
        */
        'gateway' => env('STORE_PAYMENT_GATEWAY', 'esewa'),

        'cod' => [
            'enabled' => (bool) env('STORE_COD_ENABLED', true),
            'default' => true,
            'max_order_total' => (float) env('STORE_COD_MAX_TOTAL', 15000),
        ],

        'esewa' => [
            'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),
            'secret' => env('ESEWA_SECRET', '8gBm/:&EnhH.1/q'),
            'test_mode' => (bool) env('ESEWA_TEST_MODE', true),
        ],

        'khalti' => [
            'public_key' => env('KHALTI_PUBLIC_KEY'),
            'secret_key' => env('KHALTI_SECRET_KEY'),
            'test_mode' => (bool) env('KHALTI_TEST_MODE', true),
        ],
    ],

];
