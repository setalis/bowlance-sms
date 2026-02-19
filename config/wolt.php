<?php

return [
    'drive' => [
        'enabled' => env('WOLT_DRIVE_ENABLED', false),
        'base_url' => env('WOLT_DRIVE_BASE_URL', 'https://daas-public-api.development.dev.woltapi.com'),
        'token' => env('WOLT_DRIVE_TOKEN'),
        /*
         * Integration mode: "venueful" (recommended) or "venueless".
         * Venueful: venues pre-configured in Wolt, use shipment-promises + deliveries.
         * Venueless: provide pickup details with each request, use delivery-order.
         */
        'mode' => env('WOLT_DRIVE_MODE', 'venueful'),
        'venue_id' => env('WOLT_DRIVE_VENUE_ID'),
        'merchant_id' => env('WOLT_DRIVE_MERCHANT_ID'),
        'currency' => env('WOLT_DRIVE_CURRENCY', 'GEL'),
        'min_preparation_time_minutes' => (int) env('WOLT_DRIVE_MIN_PREP_MINUTES', 15),
        /*
         * Город по умолчанию для доставки (для binding promise нужны street + city).
         * Если в адресе нет запятой, используется этот город, а строка — как street.
         */
        'default_delivery_city' => env('WOLT_DRIVE_DEFAULT_CITY', 'Batumi'),
        'pickup' => [
            'name' => env('WOLT_DRIVE_PICKUP_NAME', env('APP_NAME', 'Bowlance')),
            'phone' => env('WOLT_DRIVE_PICKUP_PHONE'),
            'address' => env('WOLT_DRIVE_PICKUP_ADDRESS'),
            'lat' => env('WOLT_DRIVE_PICKUP_LAT'),
            'lng' => env('WOLT_DRIVE_PICKUP_LNG'),
        ],
        'customer_support' => [
            'email' => env('WOLT_DRIVE_SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS')),
            'phone_number' => env('WOLT_DRIVE_SUPPORT_PHONE'),
            'url' => env('WOLT_DRIVE_SUPPORT_URL'),
        ],
    ],
];
