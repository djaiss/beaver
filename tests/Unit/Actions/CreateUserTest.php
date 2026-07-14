<?php

declare(strict_types=1);
use App\Actions\CreateUser;
use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

it('creates a user in an account', function () {
    Date::setTestNow(Date::create(2018, 1, 1));

    $account = Account::factory()->create();

    $user = new CreateUser(
        account: $account,
        email: 'chandler.bing@friends.com',
        password: 'password',
        firstName: 'Chandler',
        lastName: 'Bing',
    )->execute();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->account_id)->toBe($account->id);
    expect($user->role)->toBe(PermissionEnum::Viewer->value);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'chandler.bing@friends.com',
        'trial_ends_at' => '2018-01-31 00:00:00',
    ]);
});

it('cant create a user with the same email', function () {
    $account = Account::factory()->create();

    User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $this->expectException(UniqueConstraintViolationException::class);

    new CreateUser(
        account: $account,
        email: 'chandler.bing@friends.com',
        password: 'password',
        firstName: 'Chandler',
        lastName: 'Bing',
    )->execute();
});
