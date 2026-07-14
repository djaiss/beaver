<?php

declare(strict_types=1);
use App\Actions\CreateAccount;
use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates an account and its first owner', function () {
    Queue::fake();

    $user = new CreateAccount(
        email: 'monica.geller@friends.com',
        password: 'password',
        firstName: 'Monica',
        lastName: 'Geller',
    )->execute();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->role)->toBe(PermissionEnum::Owner->value);
    expect($user->email)->toBe('monica.geller@friends.com');

    $account = $user->account;
    expect($account)->toBeInstanceOf(Account::class);
    expect($account->name)->toBe('Monica Geller');
    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'created_by_id' => $user->id,
        'updated_by_id' => $user->id,
    ]);
    expect($account->created_by_name)->toBe('Monica Geller');
    expect($account->updated_by_name)->toBe('Monica Geller');
});

it('sanitizes the name it derives the account from', function () {
    Queue::fake();

    $user = new CreateAccount(
        email: 'monica.geller@friends.com',
        password: 'password',
        firstName: '<strong>Monica</strong>',
        lastName: 'Geller',
    )->execute();

    expect($user->account->name)->toBe('Monica Geller');
});
