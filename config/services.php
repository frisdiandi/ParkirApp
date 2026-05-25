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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ocr_space' => [
        // Daftar gratis API key di: https://ocr.space/ocrapi/freekey (25.000 req/bulan)
        // 'helloworld' adalah demo key publik dengan limit 1 req / 10 detik
        'key'      => env('OCR_SPACE_API_KEY', 'helloworld'),
        'endpoint' => env('OCR_SPACE_ENDPOINT', 'https://api.ocr.space/parse/image'),
        'language' => env('OCR_SPACE_LANGUAGE', 'eng'),
        'engine'   => (int) env('OCR_SPACE_ENGINE', 2),
    ],

    'bank_nagari' => [
        'endpoint'      => env('BANK_NAGARI_ENDPOINT', 'https://demo.banknagari.co.id:7810/APIAgregatorService/Services/PublicRequest'),
        'authorization' => env('BANK_NAGARI_AUTHORIZATION', 'BN UEox'),
        'secret_key'    => env('BANK_NAGARI_SECRET_KEY', 'BKD*#*@PJ1QRS!BN=='),
        'request_id'    => env('BANK_NAGARI_REQUEST_ID', 'QR03'),
        'outlet_id'     => env('BANK_NAGARI_OUTLET_ID', '007210024'),
        'pjsp'          => env('BANK_NAGARI_PJSP', 'NGR'),
        // SSL verify off untuk demo server self-signed
        'verify'        => (bool) env('BANK_NAGARI_VERIFY_SSL', false),
    ],

];
