<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('fills the press address into the page in the browser', function () {
    // The address is never written into the html: the page ships the characters exclusive
    // ored with a key and a script that puts them back. Nothing but a real browser can say
    // whether a reader ends up seeing the address, so this is where that is checked.
    $page = visit('/en/media-kit');

    $page->assertSee(config('marketing.press_email'))
        ->assertNoJavascriptErrors();
});
