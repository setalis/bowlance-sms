<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your Vonage API key from the Vonage Dashboard
    |
    */

    'api_key' => env('VONAGE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | API Secret
    |--------------------------------------------------------------------------
    |
    | Your Vonage API secret from the Vonage Dashboard
    |
    */

    'api_secret' => env('VONAGE_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Signature Secret
    |--------------------------------------------------------------------------
    |
    | Optional signature secret for webhook security
    |
    */

    'signature_secret' => env('VONAGE_SIGNATURE_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Application ID
    |--------------------------------------------------------------------------
    |
    | Your Vonage Application ID (for Voice/Messages API)
    |
    */

    'application_id' => env('VONAGE_APPLICATION_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Private Key
    |--------------------------------------------------------------------------
    |
    | Path to your private key file or the key contents
    |
    */

    'private_key' => env('VONAGE_PRIVATE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    |
    | HTTP client to use for API requests
    |
    */

    'http_client' => env('VONAGE_HTTP_CLIENT'),

];
