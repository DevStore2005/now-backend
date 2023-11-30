<?php

return [
    'domain' => [
        'admin' => env('ADMIN_DOMAIN', 'admin.site.local'),
        'restaurant' => env('RESTAURANT_DOMAIN', 'restaurant.site.local'),
        'grocer' => env('GROCER_DOMAIN', 'grocer.site.local'),
        'api' => env('API_DOMAIN', 'api.site.local'),
    ],
    'flutterwave' => [
        'wave_key' => env('FLUTTER_WAVE_KEY'),
        'secret_key' => env('FLUTTER_WAVE_SECRET_KEY'),
        'secret_hash' => env('FLUTTER_WAVE_SECRET_HASH', '12345678'),
    ],
];
