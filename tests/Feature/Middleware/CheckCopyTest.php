<?php

declare(strict_types=1);

use App\Enums\TransactionType;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('resolves a copy of the item in the url', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)
        ->post('/collections/'.$catalog->id.'/items/'.$item->id.'/copies/'.$copy->id.'/transactions', [
            'type' => TransactionType::Purchase->value,
            'occurred_at' => '2026-01-05',
        ])
        ->assertRedirect();

    expect($copy->transactions()->count())->toBe(1);
});

it('does not find a copy that belongs to another item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $otherItem = Item::factory()->create(['catalog_id' => $catalog->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $otherItem->id]);

    $this->actingAs($user)
        ->post('/collections/'.$catalog->id.'/items/'.$item->id.'/copies/'.$otherCopy->id.'/transactions', [
            'type' => TransactionType::Purchase->value,
            'occurred_at' => '2026-01-05',
        ])
        ->assertNotFound();

    expect($otherCopy->transactions()->count())->toBe(0);
});

it('does not find a copy that does not exist', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)
        ->post('/collections/'.$catalog->id.'/items/'.$item->id.'/copies/404404/transactions', [
            'type' => TransactionType::Purchase->value,
            'occurred_at' => '2026-01-05',
        ])
        ->assertNotFound();
});
