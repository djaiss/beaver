<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Spam protection
    |--------------------------------------------------------------------------
    |
    | Whether the sign in, sign up and password reset forms carry a Cloudflare
    | Turnstile widget. It is off by default, which is what a self hosted
    | instance wants: nobody has to open a Cloudflare account to run this
    | application. Turn it on with TURNSTILE_ENABLED=true, and fill in both keys
    | below, since the flag alone protects nothing.
    |
    | Note that with the flag on, the instance has to reach Cloudflare for
    | anyone to sign in. A verification we cannot perform is a verification that
    | fails.
    |
    */

    'enabled' => (bool) env('TURNSTILE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Turnstile keys
    |--------------------------------------------------------------------------
    |
    | The pair Cloudflare hands you when you create a widget. The site key is
    | public and ends up in the page, the secret key never leaves the server and
    | is what verifies the token the visitor solved.
    |
    | https://developers.cloudflare.com/turnstile/get-started/
    |
    */

    'site_key' => env('TURNSTILE_SITE_KEY'),

    'secret_key' => env('TURNSTILE_SECRET_KEY'),

];
