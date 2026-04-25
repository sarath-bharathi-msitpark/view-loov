<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // 'cashfree' => [
    //     'key' => '',
    //     'secret' => '',
    //     'url' => 'https://api.cashfree.com/pg/orders',
    //     'subscription_url' => 'https://sandbox.cashfree.com',
    //     'mode' => 'sandbox' // production
    // ],
    'cashfree' => [
        'mode' => env('CASHFREE_MODE', 'sandbox'), // sandbox or production
    
        'sandbox' => [
            'key' => env('CASHFREE_SANDBOX_KEY', ''),
            'secret' => env('CASHFREE_SANDBOX_SECRET', ''),
            'base_url' => 'https://sandbox.cashfree.com',
            'api_url' => 'https://sandbox.cashfree.com/pg/orders',
        ],
    
        'production' => [
            'key' => env('CASHFREE_LIVE_KEY', ''),
            'secret' => env('CASHFREE_LIVE_SECRET', ''),
            'base_url' => 'https://api.cashfree.com',
            'api_url' => 'https://api.cashfree.com/pg/orders',
        ],
    ],


    'desktop_apk_integration' => [
        'access_key' => env('DESKTOP_INTEGRATION_ACCESS_KEY'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'stripe' => [
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],


];
