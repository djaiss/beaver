<?php

declare(strict_types=1);
use App\Enums\DatePrecision;
use App\Enums\MaintenanceType;
use App\Enums\PermissionEnum;
use App\Enums\TransactionType;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Item;
use App\Models\LocationHistory;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function copyForApiHistory(int $accountId): Copy
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
            'source_type',
            'source_id',
            'title',
            'summary',
            'date',
            'date_precision',
            'amount',
            'currency_code',
            'meaningful',
        ],
    ];
});

it('returns the meaningful history of a copy, newest first', function () {
    $user = $this->createUser();
    $copy = copyForApiHistory($user->account_id);

    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01', 'amount' => 450000, 'currency_code' => 'CAD']);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '1987-01-01', 'occurred_at_precision' => DatePrecision::Year]);
    // A routine move: out of the meaningful view.
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2026-01-01']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/history')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => $this->jsonStructure]])
        // Newest first: the 2012 valuation reads before the 1987 acquisition.
        ->assertJsonPath('data.0.attributes.source_type', 'valuation')
        ->assertJsonPath('data.0.id', 'valuation-'.$valuation->id)
        ->assertJsonPath('data.0.attributes.amount', 450000)
        ->assertJsonPath('data.0.attributes.currency_code', 'CAD')
        ->assertJsonPath('data.1.attributes.source_type', 'provenance')
        ->assertJsonPath('data.1.attributes.date_precision', 'year');
});

it('adds the routine entries in the complete view', function () {
    $user = $this->createUser();
    $copy = copyForApiHistory($user->account_id);

    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'type' => MaintenanceType::Cleaning, 'include_in_provenance' => false, 'performed_at' => '2013-01-01']);
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2026-01-01']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/history')
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->json('GET', '/api/copies/'.$copy->id.'/history?view=complete')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('filters the history by type', function () {
    $user = $this->createUser();
    $copy = copyForApiHistory($user->account_id);

    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Purchase, 'occurred_at' => '1987-01-01']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/history?type[]=valuation')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.attributes.source_type', 'valuation');
});

it('does not return the history of a copy in another account', function () {
    $user = $this->createUser();
    $copy = copyForApiHistory($this->createAccount()->id);
    InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/history')->assertNotFound();
});

it('lets a viewer read the history', function () {
    $owner = $this->createUser();
    $viewer = $this->createUser();
    $this->assignUserToAccount($viewer, $owner->account, PermissionEnum::Viewer->value);
    $copy = copyForApiHistory($owner->account_id);
    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);

    Sanctum::actingAs($viewer);

    $this->json('GET', '/api/copies/'.$copy->id.'/history')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('requires authentication', function () {
    $copy = copyForApiHistory($this->createAccount()->id);

    $this->json('GET', '/api/copies/'.$copy->id.'/history')->assertUnauthorized();
});
