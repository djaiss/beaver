<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('edits the profile through the browser', function () {
    $user = User::factory()->create([
        'first_name' => 'Rachel',
        'last_name' => 'Green',
        'nickname' => null,
        'email' => 'rachel.green@friends.com',
    ]);

    $this->actingAs($user);

    $page = visit('/profile');

    $page->assertSee('Details')
        ->fill('first_name', 'Rachel')
        ->fill('last_name', 'Green-Geller')
        ->fill('nickname', 'Rach')
        ->press('Save')
        ->assertPathIs('/profile')
        ->assertSee('Changes saved');

    $user->refresh();
    expect($user->last_name)->toBe('Green-Geller');
    expect($user->nickname)->toBe('Rach');
});
