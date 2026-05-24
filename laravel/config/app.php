<?php

return [
    'name' => env('APP_NAME', 'Olympiad Mathematics'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Tashkent',
    'locale' => env('APP_LOCALE', 'ru'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',

    'admin_email' => env('ADMIN_EMAIL'),
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),
    ],
];
