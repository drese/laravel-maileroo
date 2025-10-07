<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maileroo API Key
    |--------------------------------------------------------------------------
    |
    | Your Maileroo API key for authentication.
    |
    */
    'api_key' => env('MAILEROO_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests to Maileroo.
    |
    */
    'timeout' => env('MAILEROO_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Default From Address
    |--------------------------------------------------------------------------
    |
    | Default from address and name for emails sent through Maileroo.
    |
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],
];