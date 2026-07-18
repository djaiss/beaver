<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = Account::factory()->create();
    $set = Set::factory()->create(['account_id' => $account->id]);

    expect($set->account)->toBeInstanceOf(Account::class);
    expect($set->account->id)->toBe($account->id);
});

it('has many items', function () {
    $set = Set::factory()->create();
    $item = Item::factory()->create(['set_id' => $set->id]);

    expect($set->items->pluck('id'))->toContain($item->id);
});

it('encrypts the name and description at rest', function () {
    $set = Set::factory()->create(['name' => 'Amazing Spider-Man #1-10', 'description' => 'The Lee run.']);

    $rawName = DB::table('sets')->where('id', $set->id)->value('name');
    $rawDescription = DB::table('sets')->where('id', $set->id)->value('description');

    $this->assertNotSame('Amazing Spider-Man #1-10', $rawName);
    expect(decrypt($rawName, false))->toBe('Amazing Spider-Man #1-10');
    expect(decrypt($rawDescription, false))->toBe('The Lee run.');
});

it('soft deletes', function () {
    $set = Set::factory()->create();

    $set->delete();

    $this->assertSoftDeleted($set);
});
