<?php
// config/services.php
return [
    // ... other services

    'tap' => [
        'auth_url' => env('TAP_AUTH_URL'),
        'payment_url' => env('TAP_PAYMENT_URL'),
        'status_url' => env('TAP_STATUS_URL'),
        'auth_token' => env('TAP_AUTH_TOKEN'),
        'api_key' => env('TAP_API_KEY'),
        'username' => env('TAP_USERNAME'),
        'password' => env('TAP_PASSWORD'),
    ],
];
