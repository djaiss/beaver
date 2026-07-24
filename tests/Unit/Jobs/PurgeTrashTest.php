<?php

declare(strict_types=1);
use App\Jobs\PurgeTrash;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permanently deletes what has sat in the trash past the retention window', function () {
    $account = $this->createAccount();
    $expired = Catalog::factory()->create(['account_id' => $account->id]);
    $expired->delete();
    $expired->forceFill(['deleted_at' => now()->subDays(31)])->saveQuietly();

    new PurgeTrash()->handle();

    $this->assertDatabaseMissing('catalogs', ['id' => $expired->id]);
});

it('keeps what is still within the retention window', function () {
    $account = $this->createAccount();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $set = Set::factory()->forAccount($account->id)->create();

    $item->delete();
    $set->delete();
    $set->forceFill(['deleted_at' => now()->subDays(29)])->saveQuietly();

    new PurgeTrash()->handle();

    $this->assertSoftDeleted($item);
    $this->assertSoftDeleted($set);
});

it('leaves objects that were never deleted alone', function () {
    $account = $this->createAccount();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    new PurgeTrash()->handle();

    $this->assertDatabaseHas('catalogs', ['id' => $catalog->id]);
});
