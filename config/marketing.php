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

    /*
    |--------------------------------------------------------------------------
    | Press contact
    |--------------------------------------------------------------------------
    |
    | The address the media kit publishes for journalists. Set it to null on an
    | instance that does not want to advertise a mailbox: the page then sends
    | people to the GitHub discussions instead.
    |
    */

    'press_email' => env('PRESS_EMAIL', 'press@getkollek.com'),

    /*
    |--------------------------------------------------------------------------
    | Public page caching
    |--------------------------------------------------------------------------
    |
    | The public site is rendered once and then held by whatever cache sits in
    | front of it. The CDN keeps a page for a week and is emptied on demand
    | (see App\Services\CloudflareCache); the visitor's own browser keeps it for
    | five minutes, because no purge can reach that copy.
    |
    */

    'cache_public_pages' => (bool) env('CACHE_PUBLIC_PAGES', true),

    'browser_cache_seconds' => (int) env('PUBLIC_PAGE_BROWSER_CACHE', 300),

    'cdn_cache_seconds' => (int) env('PUBLIC_PAGE_CDN_CACHE', 60 * 60 * 24 * 7),

];
