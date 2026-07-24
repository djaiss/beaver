<?php

declare(strict_types=1);
use App\Enums\InsuranceStatus;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('records coverage against a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->post(route('insuranceRecords.create', [$catalog, $item, $copy]), [
        'provider' => 'Collectibles Insurance Services',
        'insured_value' => '450.50',
        'status' => InsuranceStatus::Active->value,
        'currency' => 'EUR',
        'policy_number' => 'CIS-88231',
        'deductible_amount' => '100',
        'starts_at' => '2024-01-01',
        'is_scheduled_item' => '1',
        'contact_name' => 'Dana Whitfield',
    ]);

    $response->assertRedirect(route('items.history.show', [$catalog, $item, $copy, 'insurance']));
    $response->assertSessionHas('status', 'Insurance record added');

    $record = InsuranceRecord::query()->first();
    expect($record->copy_id)->toBe($copy->id);
    expect($record->provider)->toBe('Collectibles Insurance Services');
    expect($record->insured_value)->toBe(45050);
    expect($record->currency_code)->toBe('EUR');
    expect($record->status)->toBe(InsuranceStatus::Active);
    expect($record->is_scheduled_item)->toBeTrue();
});

it('converts the money fields from currency units to cents', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('insuranceRecords.create', [$catalog, $item, $copy]), [
        'provider' => 'Allianz',
        'insured_value' => '120.50',
        'status' => InsuranceStatus::Active->value,
        'deductible_amount' => '25.25',
    ]);

    $record = InsuranceRecord::query()->first();
    expect($record->insured_value)->toBe(12050);
    expect($record->deductible_amount)->toBe(2525);
});

it('requires a provider, an insured value and a status', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('insuranceRecords.create', [$catalog, $item, $copy]), [])
        ->assertSessionHasErrors(['provider', 'insured_value', 'status']);
});

// The active-per-policy rule surfaces as a validation error rather than a 500.
it('rejects a second active record for the same policy', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);

    $this->actingAs($user)->post(route('insuranceRecords.create', [$catalog, $item, $copy]), [
        'provider' => 'Collectibles Insurance Services',
        'insured_value' => '500',
        'status' => InsuranceStatus::Active->value,
        'policy_number' => 'CIS-88231',
    ])->assertSessionHasErrors(['status']);
});

it('does not record coverage against a copy of another account', function () {
    $user = $this->createUser();
    $otherCatalog = Catalog::factory()->create(['account_id' => $this->createAccount()->id]);
    $otherItem = Item::factory()->create(['catalog_id' => $otherCatalog->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $otherItem->id]);

    $this->actingAs($user)->post(route('insuranceRecords.create', [$otherCatalog, $otherItem, $otherCopy]), [
        'provider' => 'Allianz',
        'insured_value' => '100',
        'status' => InsuranceStatus::Active->value,
    ])->assertNotFound();
});

it('forbids a viewer from recording coverage', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->post(route('insuranceRecords.create', [$catalog, $item, $copy]), [
        'provider' => 'Allianz',
        'insured_value' => '100',
        'status' => InsuranceStatus::Active->value,
    ])->assertNotFound();
});

it('updates a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'insured_value' => 30000,
    ]);

    $response = $this->actingAs($user)->put(route('insuranceRecords.update', [$catalog, $item, $copy, $record]), [
        'provider' => 'Allstate',
        'insured_value' => '199.99',
        'status' => InsuranceStatus::Expired->value,
    ]);

    $response->assertRedirect(route('items.history.show', [$catalog, $item, $copy, 'insurance']));
    $response->assertSessionHas('status', 'Insurance record updated');

    $record->refresh();
    expect($record->provider)->toBe('Allstate');
    expect($record->insured_value)->toBe(19999);
    expect($record->status)->toBe(InsuranceStatus::Expired);
});

it('does not update a record that belongs to another copy', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $item->id]);
    $record = InsuranceRecord::factory()->create(['copy_id' => $otherCopy->id]);

    $this->actingAs($user)->put(route('insuranceRecords.update', [$catalog, $item, $copy, $record]), [
        'provider' => 'Allianz',
        'insured_value' => '100',
        'status' => InsuranceStatus::Active->value,
    ])->assertNotFound();
});

it('deletes a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    $response = $this->actingAs($user)->delete(route('insuranceRecords.destroy', [$catalog, $item, $copy, $record]));

    $response->assertRedirect(route('items.history.show', [$catalog, $item, $copy, 'insurance']));
    $this->assertModelMissing($record);
});

it('forbids a viewer from deleting a record', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->delete(route('insuranceRecords.destroy', [$catalog, $item, $copy, $record]))->assertNotFound();
});

// The insurance section renders the panel with its records.
it('shows the insurance records of a copy on the history tab', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'insured_value' => 45000]);

    $this->actingAs($user)->get(route('items.history.show', [$catalog, $item, $copy, 'insurance']))
        ->assertOk()
        ->assertSee('data-test="insurance-'.$record->id.'"', false)
        ->assertSee('data-test="new-insurance-'.$copy->id.'"', false);
});

// The add card carries the whole form: the segmented status control, the
// scheduled toggle and the submit, so a regression in its shape shows up here.
it('renders the add form with its status buttons and scheduled toggle', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$catalog, $item, $copy, 'insurance']))
        ->assertOk()
        ->assertSee('data-test="add-insurance-'.$copy->id.'-status-active"', false)
        ->assertSee('data-test="add-insurance-'.$copy->id.'-status-cancelled"', false)
        ->assertSee('data-test="add-insurance-'.$copy->id.'-scheduled-toggle"', false)
        ->assertSee('data-test="add-insurance-'.$copy->id.'-submit"', false);
});

it('does not render the insurance form for a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->get(route('items.history.show', [$catalog, $item, $copy, 'insurance']))
        ->assertOk()
        ->assertDontSee('data-test="new-insurance-'.$copy->id.'"', false);
});
