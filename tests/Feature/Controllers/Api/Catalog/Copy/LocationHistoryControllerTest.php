<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\LocationHistory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * A copy belonging to the given account.
 */
function copyToLocateForAccount(int $accountId): Copy
{
    $catalog = Catalog::factory()->create(['account_id' => $accountId]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'copy_id',
            'location_id',
            'moved_at',
            'moved_out_at',
            'reason',
            'note',
            'is_open',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the location history of a copy', function () {
    $user = $this->createUser();
    $copy = copyToLocateForAccount($user->account_id);
    LocationHistory::factory()->create(['copy_id' => $copy->id]);
    LocationHistory::factory()->closed()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/location-history')
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
    $copy = copyToLocateForAccount($this->createAccount()->id);
    LocationHistory::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/location-history')->assertNotFound();
});

it('shows a record', function () {
    $user = $this->createUser();
    $copy = copyToLocateForAccount($user->account_id);
    $location = Location::factory()->create(['account_id' => $user->account_id]);
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $location->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/location-history/'.$record->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'location_history')
        ->assertJsonPath('data.id', (string) $record->id)
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.location_id', (string) $location->id)
        ->assertJsonPath('data.attributes.is_open', true)
        ->assertJsonPath('data.links.self', route('api.copies.locationHistory.show', [$copy->id, $record->id]));
});

it('moves a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLocateForAccount($user->account_id);
    $shelf = Location::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/location-history', [
        'location_id' => $shelf->id,
        'moved_at' => '2024-01-01',
        'reason' => 'Catalogued',
    ])
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.location_id', (string) $shelf->id)
        ->assertJsonPath('data.attributes.is_open', true);

    expect($copy->refresh()->current_location_id)->toBe($shelf->id);
});

it('requires a destination and a date', function () {
    $user = $this->createUser();
    $copy = copyToLocateForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/location-history', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['location_id', 'moved_at']);
});

it('does not move a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToLocateForAccount($this->createAccount()->id);
    $location = Location::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/location-history', [
        'location_id' => $location->id,
        'moved_at' => '2024-01-01',
    ])->assertNotFound();
});

it('restricts moving to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToLocateForAccount($account->id);
    $location = Location::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/location-history', [
        'location_id' => $location->id,
        'moved_at' => '2024-01-01',
    ])->assertNotFound();
});

it('corrects a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLocateForAccount($user->account_id);
    $shelf = Location::factory()->create(['account_id' => $user->account_id]);
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $shelf->id, 'moved_at' => '2024-01-01']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/location-history/'.$record->id, [
        'location_id' => $shelf->id,
        'moved_at' => '2024-02-15',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.moved_at', Carbon::parse('2024-02-15')->timestamp);

    expect($record->refresh()->moved_at->toDateString())->toBe('2024-02-15');
});

it('deletes a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLocateForAccount($user->account_id);
    $record = LocationHistory::factory()->closed()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/location-history/'.$record->id)->assertNoContent();

    $this->assertModelMissing($record);
});

it('restricts deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToLocateForAccount($account->id);
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/location-history/'.$record->id)->assertNotFound();
});
