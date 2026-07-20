<?php

declare(strict_types=1);
use App\Enums\MaintenanceType;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * A copy belonging to the given account.
 */
function copyToMaintainForAccount(int $accountId): Copy
{
    $collection = Collection::factory()->create(['account_id' => $accountId]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'copy_id',
            'provenance_event_id',
            'type',
            'title',
            'description',
            'performed_by',
            'performed_at',
            'cost_amount',
            'cost_currency_code',
            'condition_before_id',
            'condition_after_id',
            'next_due_at',
            'include_in_provenance',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the maintenance records of a copy', function () {
    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/maintenance-records')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list the records of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToMaintainForAccount($this->createAccount()->id);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/maintenance-records')->assertNotFound();
});

it('shows a record', function () {
    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);
    $record = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'type' => MaintenanceType::Conservation,
        'title' => 'Archival cleaning',
        'cost_amount' => 12000,
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/maintenance-records/'.$record->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'maintenance_record')
        ->assertJsonPath('data.id', (string) $record->id)
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.type', 'conservation')
        ->assertJsonPath('data.attributes.title', 'Archival cleaning')
        ->assertJsonPath('data.attributes.cost_amount', 12000)
        ->assertJsonPath('data.links.self', route('api.copies.maintenanceRecords.show', [$copy->id, $record->id]));
});

it('creates a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/copies/'.$copy->id.'/maintenance-records', [
        'type' => MaintenanceType::Repair->value,
        'title' => 'Spine repair',
        'cost_amount' => 5000,
        'cost_currency_code' => 'USD',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.type', 'repair')
        ->assertJsonPath('data.attributes.title', 'Spine repair')
        ->assertJsonPath('data.attributes.cost_amount', 5000);

    $record = MaintenanceRecord::query()->latest('id')->first();
    expect($record->copy_id)->toBe($copy->id);
});

it('updates the copy condition through the condition after', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);
    $after = Condition::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/maintenance-records', [
        'type' => MaintenanceType::Restoration->value,
        'title' => 'Full restoration',
        'condition_after_id' => $after->id,
    ])->assertCreated();

    expect($copy->refresh()->condition_id)->toBe($after->id);
});

it('generates a provenance event when marked for provenance', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/maintenance-records', [
        'type' => MaintenanceType::Restoration->value,
        'title' => 'Museum-grade restoration',
        'include_in_provenance' => true,
    ])->assertCreated();

    expect(ProvenanceEvent::query()->count())->toBe(1);
});

it('requires a type and a title', function () {
    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/maintenance-records', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'title']);
});

it('does not create a record on a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToMaintainForAccount($this->createAccount()->id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/maintenance-records', [
        'type' => MaintenanceType::Cleaning->value,
        'title' => 'Cleaning',
    ])->assertNotFound();
});

it('restricts record creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToMaintainForAccount($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/maintenance-records', [
        'type' => MaintenanceType::Cleaning->value,
        'title' => 'Cleaning',
    ])->assertNotFound();
});

it('updates a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);
    $record = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Old title',
    ]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/maintenance-records/'.$record->id, [
        'type' => MaintenanceType::Restoration->value,
        'title' => 'Full restoration',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.type', 'restoration')
        ->assertJsonPath('data.attributes.title', 'Full restoration');

    expect($record->refresh()->title)->toBe('Full restoration');
});

it('restricts record updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToMaintainForAccount($account->id);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/copies/'.$copy->id.'/maintenance-records/'.$record->id, [
        'type' => MaintenanceType::Cleaning->value,
        'title' => 'Cleaning',
    ])->assertNotFound();
});

it('deletes a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToMaintainForAccount($user->account_id);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/maintenance-records/'.$record->id)->assertNoContent();

    $this->assertModelMissing($record);
});

it('restricts record deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToMaintainForAccount($account->id);
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/maintenance-records/'.$record->id)->assertNotFound();
});
