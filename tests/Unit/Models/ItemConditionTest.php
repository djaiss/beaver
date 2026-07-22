<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\ItemCondition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);

    expect($condition->account)->toBeInstanceOf(Account::class);
    expect($condition->account->id)->toBe($account->id);
});

it('encrypts the name at rest', function () {
    $condition = ItemCondition::factory()->create(['name' => 'New']);

    $rawName = DB::table('item_conditions')->where('id', $condition->id)->value('name');

    $this->assertNotSame('New', $rawName);
    expect($condition->fresh()->name)->toBe('New');
});

it('is a system default when it has no account', function () {
    $condition = ItemCondition::factory()->systemDefault()->create();

    expect($condition->account_id)->toBeNull();
    expect($condition->isSystemDefault())->toBeTrue();
});

it('is not a system default when it belongs to an account', function () {
    $account = $this->createAccount();
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);

    expect($condition->isSystemDefault())->toBeFalse();
});
