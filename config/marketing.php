<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Marketing site
    |--------------------------------------------------------------------------
    |
    | Whether the public marketing site (the homepage and the API reference) is
    | served. Self hosted instances rarely need it, so it stays off by default
    | and every marketing route redirects to the application instead.
    |
    */

    'show' => (bool) env('SHOW_MARKETING_SITE', false),

    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    |
    | Where every "View on GitHub" link on the marketing site points to.
    |
    */

    'github_url' => 'https://github.com/djaiss/kollek',

];
