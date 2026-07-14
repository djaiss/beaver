<?php

declare(strict_types=1);
use App\Enums\VisibilityEnum;
use App\Models\Account;
use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    expect($collection->account)->toBeInstanceOf(Account::class);
    expect($collection->account->id)->toBe($account->id);
});

it('generates a uuid on creation when none is given', function () {
    $account = $this->createAccount();

    $collection = Collection::query()->create([
        'account_id' => $account->id,
        'name' => 'Marvel Comics 1990s',
    ]);

    expect($collection->uuid)->toBeString();
    expect($collection->uuid)->not->toBeEmpty();
});

it('encrypts the name at rest', function () {
    $collection = Collection::factory()->create(['name' => 'Marvel Comics 1990s']);

    $rawName = DB::table('collections')->where('id', $collection->id)->value('name');

    $this->assertNotSame('Marvel Comics 1990s', $rawName);
    expect($collection->fresh()->name)->toBe('Marvel Comics 1990s');
});

it('casts the visibility to an enum', function () {
    $collection = Collection::factory()->create(['visibility' => VisibilityEnum::Public->value]);

    expect($collection->fresh()->visibility)->toBe(VisibilityEnum::Public);
});

it('casts the settings to an array', function () {
    $collection = Collection::factory()->create(['settings' => ['theme' => 'dark']]);

    expect($collection->fresh()->settings)->toBe(['theme' => 'dark']);
});

it('soft deletes', function () {
    $collection = Collection::factory()->create();

    $collection->delete();

    $this->assertSoftDeleted($collection);
    expect(Collection::query()->find($collection->id))->toBeNull();
    expect(Collection::withTrashed()->find($collection->id))->not->toBeNull();
});
