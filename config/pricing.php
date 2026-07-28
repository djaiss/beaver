<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Hosted instance
    |--------------------------------------------------------------------------
    |
    | Whether this instance is the managed service we run and charge for. It is
    | off by default, so a self hosted instance has no item limit, no upgrade
    | screen and nothing to buy: that is the whole point of the MIT licence.
    | Turn it on with HOSTED_INSTANCE=true and the free allowance below starts
    | to apply.
    |
    */

    'hosted' => (bool) env('HOSTED_INSTANCE', false),

    /*
    |--------------------------------------------------------------------------
    | Free allowance
    |--------------------------------------------------------------------------
    |
    | How many items a hosted account may hold before it has to be unlocked.
    | The first ten are free. The five after them are a grace: the account
    | still accepts them, but every screen says it has outgrown the free plan.
    | Past the sum of the two, adding an item is refused.
    |
    */

    'free_item_limit' => 10,

    'grace_items' => 5,

    /*
    |--------------------------------------------------------------------------
    | Price
    |--------------------------------------------------------------------------
    |
    | What unlocking an account costs, in whole US dollars, once. There is no
    | subscription and no second invoice.
    |
    */

    'price' => 49,

];
