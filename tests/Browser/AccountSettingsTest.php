<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('chooses the default currency of the account', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/settings');

    $page->assertSee('Default currency');

    // The dropdown lists each currency as its flag followed by the code.
    expect($page->content())->toContain('🇺🇸 USD');
    expect($page->content())->toContain('🇪🇺 EUR');

    $page->select('currency_code', 'EUR')
        ->press('Save')
        ->assertPathIs('/settings')
        ->assertSee('Account updated successfully');

    expect($user->account->fresh()->currency_code)->toBe('EUR');
});
