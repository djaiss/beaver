<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a new user through the browser', function () {
    $page = visit('/register');

    $page->assertSee('Sign up for an account')
        ->fill('first_name', 'Chandler')
        ->fill('last_name', 'Bing')
        ->fill('email', 'chandler.bing@friends.com')
        ->fill('password', '5UTHSmdj')
        ->fill('password_confirmation', '5UTHSmdj')
        ->submit()
        ->assertPathIs('/verify-email')
        ->assertSee('Thanks for signing up!');

    expect(User::query()->count())->toBe(1);
});
