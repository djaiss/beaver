<?php

declare(strict_types=1);
use App\Jobs\PurgeTrash;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permanently deletes what has sat in the trash past the retention window', function () {
    $account = $this->createAccount();
    $expired = Collection::factory()->create(['account_id' => $account->id]);
    $expired->delete();
    $expired->forceFill(['deleted_at' => now()->subDays(31)])->saveQuietly();

    new PurgeTrash()->handle();

    $this->assertDatabaseMissing('collections', ['id' => $expired->id]);
});

it('keeps what is still within the retention window', function () {
    $account = $this->createAccount();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $set = Set::factory()->create(['account_id' => $account->id]);

    $item->delete();
    $set->delete();
    $set->forceFill(['deleted_at' => now()->subDays(29)])->saveQuietly();

    new PurgeTrash()->handle();

    $this->assertSoftDeleted($item);
    $this->assertSoftDeleted($set);
});

it('leaves objects that were never deleted alone', function () {
    $account = $this->createAccount();
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    new PurgeTrash()->handle();

    $this->assertDatabaseHas('collections', ['id' => $collection->id]);
});
