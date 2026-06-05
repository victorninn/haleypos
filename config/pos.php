<?php

return [
    /*
    |--------------------------------------------------------------------------
    | POS Branding
    |--------------------------------------------------------------------------
    |
    | Change these values (or the underlying ENV vars) to rebrand the POS.
    | The brand name is shown in headers, receipts and tab titles.
    | The logo path is relative to the /public directory.
    |
    */

    'brand_name' => env('POS_BRAND_NAME', 'Haleys'),

    'brand_logo' => env('POS_BRAND_LOGO', '/assets/logo.svg'),

   /* 'tagline' => env('POS_BRAND_TAGLINE', 'Indoor playhouse, sorted.'), */

    'currency' => env('POS_CURRENCY', 'INR'),

    'currency_symbol' => env('POS_CURRENCY_SYMBOL', '$'),

    /*
    | Status thresholds (in minutes) for tablet dashboard chips.
    | green: remaining > yellow_at; yellow: remaining > red_at; red: <= red_at
    */
    'yellow_at' => env('POS_STATUS_YELLOW_AT', 20),
    'red_at'    => env('POS_STATUS_RED_AT', 5),
];
