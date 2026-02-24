<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Tokyo',
    'locale' => 'ja',
    'fallback_locale' => 'en',
    'faker_locale' => 'ja_JP',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    'auth_code' => env('AUTH_CODE', ''),
];

