<?php

return [

    'env' => env('DOKU_ENV', 'sandbox'),

    'client_id' => env('DOKU_CLIENT_ID'),

    'secret_key' => env('DOKU_SECRET_KEY'),

    'api_key' => env('DOKU_API_KEY'),

    'sandbox_url' => env(
        'DOKU_SANDBOX_URL',
        'https://sandbox.doku.com'
    ),

];