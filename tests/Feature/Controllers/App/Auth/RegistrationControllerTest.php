<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the create account page', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});
it('creates a user with their own account', function () {
    $response = $this->post('/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.index', absolute: false));

    $user = User::query()->where('email', 'chandler.bing@friends.com')->firstOrFail();

    // A single account was created, owned by the new user.
    $account = $user->account;
    expect($user->role)->toBe(PermissionEnum::Owner->value);
    expect($account->name)->toBe('Chandler Bing');
    expect($account->created_by_id)->toBe($user->id);
    expect(Account::query()->count())->toBe(1);
});
