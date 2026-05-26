<?php

return [
    'enabled' => (bool) env('POSTER_ENABLED', false),
    'token' => env('POSTER_TOKEN'),
    'spot_id' => (int) env('POSTER_SPOT_ID', 1),
];
