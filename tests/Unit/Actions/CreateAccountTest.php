<?php

declare(strict_types=1);
use App\Actions\CreateAccount;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an account and stamps the author', function () {
    $author = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);

    $account = new CreateAccount(
        author: $author,
        name: 'Central Perk',
    )->execute();

    expect($account)->toBeInstanceOf(Account::class);
    expect($account->name)->toBe('Central Perk');
    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'created_by_id' => $author->id,
        'updated_by_id' => $author->id,
    ]);
    expect($account->created_by_name)->toBe('Monica Geller');
    expect($account->updated_by_name)->toBe('Monica Geller');
});
it('sanitizes the name', function () {
    $author = $this->createUser();

    $account = new CreateAccount(
        author: $author,
        name: '<strong>Central Perk</strong>',
    )->execute();

    expect($account->name)->toBe('Central Perk');
});
