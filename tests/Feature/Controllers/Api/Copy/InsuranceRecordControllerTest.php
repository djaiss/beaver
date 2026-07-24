<?php

declare(strict_types=1);
use App\Enums\InsuranceStatus;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * A copy belonging to the given account.
 */
function copyToInsureForAccount(int $accountId): Copy
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
            'provider',
            'policy_number',
            'coverage_type',
            'insured_value',
            'currency_code',
            'deductible_amount',
            'deductible_currency_code',
            'starts_at',
            'ends_at',
            'status',
            'is_scheduled_item',
            'contact_name',
            'contact_email',
            'contact_phone',
            'note',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the insurance records of a copy', function () {
    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);
    InsuranceRecord::factory()->create(['copy_id' => $copy->id]);
    InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'status' => InsuranceStatus::Expired]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/insurance-records')
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
    $copy = copyToInsureForAccount($this->createAccount()->id);
    InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/insurance-records')->assertNotFound();
});

it('shows a record', function () {
    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);
    $record = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'provider' => 'Collectibles Insurance Services',
        'insured_value' => 45000,
        'status' => InsuranceStatus::Active,
        'is_scheduled_item' => true,
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/insurance-records/'.$record->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'insurance_record')
        ->assertJsonPath('data.id', (string) $record->id)
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.provider', 'Collectibles Insurance Services')
        ->assertJsonPath('data.attributes.insured_value', 45000)
        ->assertJsonPath('data.attributes.status', 'active')
        ->assertJsonPath('data.attributes.is_scheduled_item', true)
        ->assertJsonPath('data.links.self', route('api.copies.insuranceRecords.show', [$copy->id, $record->id]));
});

it('creates a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/copies/'.$copy->id.'/insurance-records', [
        'provider' => 'Collectibles Insurance Services',
        'insured_value' => 45000,
        'status' => InsuranceStatus::Active->value,
        'currency_code' => 'USD',
        'policy_number' => 'CIS-88231',
        'is_scheduled_item' => true,
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.provider', 'Collectibles Insurance Services')
        ->assertJsonPath('data.attributes.insured_value', 45000)
        ->assertJsonPath('data.attributes.status', 'active');

    $record = InsuranceRecord::query()->latest('id')->first();
    expect($record->copy_id)->toBe($copy->id);
});

it('defaults the status to active when none is given', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/insurance-records', [
        'provider' => 'Allianz',
        'insured_value' => 5000,
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.status', 'active');
});

it('requires a provider and an insured value', function () {
    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/insurance-records', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['provider', 'insured_value']);
});

it('rejects a second active record for the same policy', function () {
    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);
    InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'status' => InsuranceStatus::Active,
        'policy_number' => 'CIS-88231',
    ]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/insurance-records', [
        'provider' => 'Collectibles Insurance Services',
        'insured_value' => 50000,
        'status' => InsuranceStatus::Active->value,
        'policy_number' => 'CIS-88231',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

it('does not create a record on a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToInsureForAccount($this->createAccount()->id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/insurance-records', [
        'provider' => 'Allianz',
        'insured_value' => 5000,
    ])->assertNotFound();
});

it('restricts record creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToInsureForAccount($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/insurance-records', [
        'provider' => 'Allianz',
        'insured_value' => 5000,
    ])->assertNotFound();
});

it('updates a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);
    $record = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'insured_value' => 30000,
        'status' => InsuranceStatus::Active,
    ]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/insurance-records/'.$record->id, [
        'provider' => 'Allstate',
        'insured_value' => 19900,
        'status' => InsuranceStatus::Expired->value,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.provider', 'Allstate')
        ->assertJsonPath('data.attributes.insured_value', 19900)
        ->assertJsonPath('data.attributes.status', 'expired');

    $record->refresh();
    expect($record->insured_value)->toBe(19900);
});

it('restricts record updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToInsureForAccount($account->id);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/copies/'.$copy->id.'/insurance-records/'.$record->id, [
        'provider' => 'Allianz',
        'insured_value' => 5000,
    ])->assertNotFound();
});

it('deletes a record', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToInsureForAccount($user->account_id);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/insurance-records/'.$record->id)->assertNoContent();

    $this->assertModelMissing($record);
});

it('restricts record deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToInsureForAccount($account->id);
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/insurance-records/'.$record->id)->assertNotFound();
});
