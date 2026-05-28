<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Platform (SaaS) Branding — Superadmin only
    |--------------------------------------------------------------------------
    |
    | These values brand the Superadmin (SaaS) panel itself, independent of
    | any tenant business. Tenants never see this branding.
    |
    */

    'name'    => env('PLATFORM_NAME', 'PlayHQ'),
    'tagline' => env('PLATFORM_TAGLINE', 'SaaS control plane for playhouse POS.'),
    'logo'    => env('PLATFORM_LOGO', '/assets/logo.svg'),
    'accent'  => env('PLATFORM_ACCENT', '#22d3ee'), // cyan-400
];
