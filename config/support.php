<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | End user support
    |--------------------------------------------------------------------------
    |
    | The support section lets a signed in user open conversations with the
    | people running the instance. It is off by default: an instance without
    | anyone reading the inbox should not advertise a way to reach it. Turn it
    | on with SUPPORT_ENABLED=true, and both the sidebar link and every support
    | route come to life.
    |
    */

    'enabled' => env('SUPPORT_ENABLED', false),

];
