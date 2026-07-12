<?php

return [
    'enabled' => (bool) env('POSTER_ENABLED', false),
    'token' => env('POSTER_TOKEN'),
    'spot_id' => (int) env('POSTER_SPOT_ID', 1),
    'constructor_product_id' => (int) env('POSTER_CONSTRUCTOR_PRODUCT_ID', 0),
    'breakfast_constructor_product_id' => (int) env('POSTER_BREAKFAST_CONSTRUCTOR_PRODUCT_ID', 0),
];
