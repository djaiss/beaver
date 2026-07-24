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
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * A copy belonging to the given account.
 */
function copyToValueForAccount(int $accountId): Copy
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
            'type',
            'amount',
            'currency_code',
            'valued_at',
            'confidence',
            'valuer',
            'method',
            'source_url',
            'reference_number',
            'note',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the valuations of a copy', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);
    Valuation::factory()->create(['copy_id' => $copy->id]);
    Valuation::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/valuations')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list the valuations of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($this->createAccount()->id);
    Valuation::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/valuations')->assertNotFound();
});

it('shows a valuation', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);
    $valuation = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'type' => ValuationType::ProfessionalAppraisal,
        'amount' => 25000,
        'currency_code' => 'USD',
        'confidence' => ValuationConfidence::High,
        'note' => 'Valued after the auction.',
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/valuations/'.$valuation->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'valuation')
        ->assertJsonPath('data.id', (string) $valuation->id)
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.type', 'professional_appraisal')
        ->assertJsonPath('data.attributes.amount', 25000)
        ->assertJsonPath('data.attributes.confidence', 'high')
        ->assertJsonPath('data.attributes.note', 'Valued after the auction.')
        ->assertJsonPath('data.links.self', route('api.copies.valuations.show', [$copy->id, $valuation->id]));
});

it('does not show a valuation of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($this->createAccount()->id);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/valuations/'.$valuation->id)->assertNotFound();
});

it('creates a valuation', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/copies/'.$copy->id.'/valuations', [
        'type' => ValuationType::ProfessionalAppraisal->value,
        'amount' => 25000,
        'valued_at' => '2024-01-15',
        'currency_code' => 'USD',
        'confidence' => ValuationConfidence::High->value,
        'valuer' => 'Central Perk Appraisals',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.type', 'professional_appraisal')
        ->assertJsonPath('data.attributes.amount', 25000)
        ->assertJsonPath('data.attributes.confidence', 'high');

    $valuation = Valuation::query()->latest('id')->first();
    expect($valuation->copy_id)->toBe($copy->id);
    expect($valuation->valued_at->toDateString())->toBe('2024-01-15');
});

it('defaults the confidence to unknown when none is given', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/valuations', [
        'type' => ValuationType::UserEstimate->value,
        'amount' => 5000,
        'valued_at' => '2024-01-15',
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.confidence', 'unknown');
});

it('requires a type, an amount and a date when creating a valuation', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/valuations', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'amount', 'valued_at']);
});

it('validates the amount when creating a valuation', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/valuations', [
        'type' => ValuationType::UserEstimate->value,
        'amount' => -1,
        'valued_at' => '2024-01-15',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

it('does not create a valuation on a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToValueForAccount($this->createAccount()->id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/valuations', [
        'type' => ValuationType::UserEstimate->value,
        'amount' => 5000,
        'valued_at' => '2024-01-15',
    ])->assertNotFound();
});

it('restricts valuation creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToValueForAccount($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/valuations', [
        'type' => ValuationType::UserEstimate->value,
        'amount' => 5000,
        'valued_at' => '2024-01-15',
    ])->assertNotFound();
});

it('updates a valuation', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);
    $valuation = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'type' => ValuationType::UserEstimate,
        'amount' => 10000,
    ]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/valuations/'.$valuation->id, [
        'type' => ValuationType::MarketEstimate->value,
        'amount' => 19900,
        'valued_at' => '2024-06-15',
        'confidence' => ValuationConfidence::Medium->value,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.type', 'market_estimate')
        ->assertJsonPath('data.attributes.amount', 19900)
        ->assertJsonPath('data.attributes.confidence', 'medium');

    $valuation->refresh();
    expect($valuation->type)->toBe(ValuationType::MarketEstimate);
    expect($valuation->amount)->toBe(19900);
});

it('restricts valuation updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToValueForAccount($account->id);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/copies/'.$copy->id.'/valuations/'.$valuation->id, [
        'type' => ValuationType::UserEstimate->value,
        'amount' => 5000,
        'valued_at' => '2024-06-15',
    ])->assertNotFound();
});

it('deletes a valuation', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToValueForAccount($user->account_id);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/valuations/'.$valuation->id)->assertNoContent();

    $this->assertModelMissing($valuation);
});

it('restricts valuation deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToValueForAccount($account->id);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/valuations/'.$valuation->id)->assertNotFound();
});
