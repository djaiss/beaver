<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('records a valuation against a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->post(route('valuations.create', [$collection, $item, $copy]), [
        'type' => ValuationType::ProfessionalAppraisal->value,
        'amount' => '250.50',
        'valued_at' => '2026-03-14',
        'currency' => 'EUR',
        'confidence' => ValuationConfidence::High->value,
        'valuer' => 'Central Perk Appraisals',
        'reference_number' => 'CP-1994',
        'note' => 'Valued the day Ross said we were on a break.',
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'section' => 'valuations']));
    $response->assertSessionHas('status', 'Valuation recorded');

    $valuation = Valuation::query()->first();
    expect($valuation->copy_id)->toBe($copy->id);
    expect($valuation->type)->toBe(ValuationType::ProfessionalAppraisal);
    expect($valuation->amount)->toBe(25050);
    expect($valuation->currency_code)->toBe('EUR');
    expect($valuation->confidence)->toBe(ValuationConfidence::High);
    expect($valuation->valuer)->toBe('Central Perk Appraisals');
});

// The form collects money in currency units, and everything is stored in cents.
it('converts the amount from currency units to cents', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('valuations.create', [$collection, $item, $copy]), [
        'type' => ValuationType::UserEstimate->value,
        'amount' => '120.50',
        'valued_at' => '2026-03-14',
        'confidence' => ValuationConfidence::Unknown->value,
    ]);

    expect(Valuation::query()->first()->amount)->toBe(12050);
});

it('requires a type, an amount, a date and a confidence', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('valuations.create', [$collection, $item, $copy]), [])
        ->assertSessionHasErrors(['type', 'amount', 'valued_at', 'confidence']);
});

it('does not record a valuation against a copy of another account', function () {
    $user = $this->createUser();
    $otherCollection = Collection::factory()->create(['account_id' => $this->createAccount()->id]);
    $otherItem = Item::factory()->create(['collection_id' => $otherCollection->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $otherItem->id]);

    $this->actingAs($user)->post(route('valuations.create', [$otherCollection, $otherItem, $otherCopy]), [
        'type' => ValuationType::UserEstimate->value,
        'amount' => '100',
        'valued_at' => '2026-03-14',
        'confidence' => ValuationConfidence::Unknown->value,
    ])->assertNotFound();
});

it('forbids a viewer from recording a valuation', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->post(route('valuations.create', [$collection, $item, $copy]), [
        'type' => ValuationType::UserEstimate->value,
        'amount' => '100',
        'valued_at' => '2026-03-14',
        'confidence' => ValuationConfidence::Unknown->value,
    ])->assertNotFound();
});

it('updates a valuation', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $valuation = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'type' => ValuationType::UserEstimate,
        'amount' => 10000,
    ]);

    $response = $this->actingAs($user)->put(route('valuations.update', [$collection, $item, $copy, $valuation]), [
        'type' => ValuationType::MarketEstimate->value,
        'amount' => '199.99',
        'valued_at' => '2026-06-15',
        'confidence' => ValuationConfidence::Medium->value,
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'section' => 'valuations']));
    $response->assertSessionHas('status', 'Valuation updated');

    $valuation->refresh();
    expect($valuation->type)->toBe(ValuationType::MarketEstimate);
    expect($valuation->amount)->toBe(19999);
    expect($valuation->confidence)->toBe(ValuationConfidence::Medium);
});

it('does not update a valuation that belongs to another copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $item->id]);
    $valuation = Valuation::factory()->create(['copy_id' => $otherCopy->id]);

    $this->actingAs($user)->put(route('valuations.update', [$collection, $item, $copy, $valuation]), [
        'type' => ValuationType::UserEstimate->value,
        'amount' => '100',
        'valued_at' => '2026-06-15',
        'confidence' => ValuationConfidence::Unknown->value,
    ])->assertNotFound();
});

it('deletes a valuation', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    $response = $this->actingAs($user)->delete(route('valuations.destroy', [$collection, $item, $copy, $valuation]));

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'section' => 'valuations']));
    $this->assertModelMissing($valuation);
});

it('forbids a viewer from deleting a valuation', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->delete(route('valuations.destroy', [$collection, $item, $copy, $valuation]))->assertNotFound();
});

// The valuations section renders the panel with its list, chart and forms.
it('shows the valuations of a copy on the history tab', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 25000]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'section' => 'valuations']))
        ->assertOk()
        ->assertSee('data-test="valuation-'.$valuation->id.'"', false)
        ->assertSee('data-test="new-valuation-'.$copy->id.'"', false);
});
