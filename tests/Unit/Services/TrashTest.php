<?php

declare(strict_types=1);
use App\Enums\TrashableEnum;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Set;
use App\Services\Trash;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists every kind of soft deleted object belonging to the account', function () {
    $account = $this->createAccount();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id]);
    $set = Set::factory()->forAccount($account->id)->create();

    $collection->delete();
    $item->delete();
    $copy->delete();
    $category->delete();
    $set->delete();

    $entries = new Trash(account: $account)->entries();

    expect($entries)->toHaveCount(5);
    expect($entries->pluck('type')->all())
        ->toEqualCanonicalizing(TrashableEnum::cases());
});

it('leaves out objects that are not deleted', function () {
    $account = $this->createAccount();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    Set::factory()->forAccount($account->id)->create();

    $collection->delete();

    $entries = new Trash(account: $account)->entries();

    expect($entries)->toHaveCount(1);
    expect($entries->first()['type'])->toBe(TrashableEnum::Collection);
});

it('leaves out objects belonging to another account', function () {
    $account = $this->createAccount();
    $otherAccount = $this->createAccount('Moondance Diner');

    $collection = Collection::factory()->create(['account_id' => $otherAccount->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $collection->delete();
    $item->delete();

    expect(new Trash(account: $account)->entries())->toHaveCount(0);
});

it('sorts the most urgent rows first', function () {
    $account = $this->createAccount();
    $old = Set::factory()->forAccount($account->id)->create(['name' => 'First Pressings']);
    $recent = Set::factory()->forAccount($account->id)->create(['name' => 'Bronze Age']);

    $old->delete();
    $old->forceFill(['deleted_at' => now()->subDays(28)])->saveQuietly();
    $recent->delete();

    $entries = new Trash(account: $account)->entries();

    expect($entries->first()['name'])->toBe('First Pressings');
    expect($entries->first()['days_left'])->toBe(2);
    expect($entries->last()['days_left'])->toBe(30);
});

it('describes an item by the collection it sat in', function () {
    $account = $this->createAccount();
    $collection = Collection::factory()->create(['account_id' => $account->id, 'name' => 'Vintage Vinyl']);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Kind of Blue']);
    $item->delete();

    $entry = new Trash(account: $account)->entries()->first();

    expect($entry['name'])->toBe('Kind of Blue');
    expect($entry['subtitle'])->toBe('in Vintage Vinyl');
});
