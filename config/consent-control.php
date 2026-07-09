<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Driver
    |--------------------------------------------------------------------------
    |
    | 'config'   - reads from this file (default; a single config file is all
    |              you need to configure the package).
    | 'eloquent' - reads from a JSON field on one of your own models, so the
    |              settings can be edited at runtime (e.g. via Filament).
    |
    */
    'driver' => env('CONSENT_CONTROL_DRIVER', 'config'),

    /*
    |--------------------------------------------------------------------------
    | Eloquent Driver Settings
    |--------------------------------------------------------------------------
    |
    | Only used when driver = 'eloquent'. The field must be cast to 'array'.
    |
    */
    'eloquent' => [
        'model' => null, // e.g. App\Models\Setting::class
        'field' => 'consent_settings',
        'record_id' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Consent Version
    |--------------------------------------------------------------------------
    |
    | Bump this whenever your categories or privacy policy change. When the
    | version stored in the visitor's cookie differs, their consent is reset and
    | the banner re-appears so they can opt in again. Set to null to disable.
    |
    */
    'version' => env('CONSENT_CONTROL_VERSION', 1),

    /*
    |--------------------------------------------------------------------------
    | Cookie Settings
    |--------------------------------------------------------------------------
    */
    'cookie' => [
        'name' => env('CONSENT_COOKIE_NAME', 'consentcontrol'),
        'days' => (int) env('CONSENT_COOKIE_DAYS', 365),
        'domain' => env('CONSENT_COOKIE_DOMAIN', parse_url((string) config('app.url'), PHP_URL_HOST)),
        'path' => '/',
        'same_site' => 'lax',
        'secure' => (bool) env('CONSENT_COOKIE_SECURE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Banner Behaviour
    |--------------------------------------------------------------------------
    */
    'banner' => [
        // Opt-in: show an explicit "Reject all" button. Only needed when you
        // pre-check optional categories — with a minimal default config the OK
        // button already saves only the checked categories. When enabled,
        // "Reject all" saves only the locked mandatory categories (disabled: true).
        'reject_button' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Consent Categories
    |--------------------------------------------------------------------------
    |
    | Each key is the identifier stored in the cookie. Labels/descriptions can be
    | plain strings OR translation keys — both are resolved through __(), so the
    | defaults below ship multilingual out of the box (see resources/lang).
    |
    | Services to (de-)activate on consent are declared as data:
    |   'scripts'       => [['src' => '…', 'async' => true]]   // external <script>
    |   'inline_script' => "…"                                  // inline JavaScript
    |
    */
    'categories' => [
        'necessary' => [
            'label' => 'consent-control::consent.categories.necessary.label',
            'description' => 'consent-control::consent.categories.necessary.description',
            'checked' => true,
            'disabled' => true,
            'children' => [
                [
                    'label' => 'consent-control::consent.categories.necessary.children.settings.label',
                    'description' => 'consent-control::consent.categories.necessary.children.settings.description',
                ],
            ],
            'scripts' => [],
            'inline_script' => null,
        ],

        'analytics' => [
            'label' => 'consent-control::consent.categories.analytics.label',
            'description' => 'consent-control::consent.categories.analytics.description',
            'checked' => false,
            'disabled' => false,
            'children' => [
                [
                    'label' => 'consent-control::consent.categories.analytics.children.gtm.label',
                    'description' => 'consent-control::consent.categories.analytics.children.gtm.description',
                ],
            ],
            'scripts' => [
                // ['src' => 'https://www.googletagmanager.com/gtag/js?id=G-XXXXX', 'async' => true],
            ],
            'inline_script' => null,
        ],

        'functional' => [
            'label' => 'consent-control::consent.categories.functional.label',
            'description' => 'consent-control::consent.categories.functional.description',
            'checked' => false,
            'disabled' => false,
            'children' => [],
            'scripts' => [],
            'inline_script' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Links
    |--------------------------------------------------------------------------
    */
    'links' => [
        'privacy' => '/datenschutz/',
        'imprint' => '/impressum/',
    ],

];
