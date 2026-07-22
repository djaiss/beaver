<?php

declare(strict_types=1);
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function copyToLendForAccount(int $accountId): Copy
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
            'loan_provenance_event_id',
            'return_provenance_event_id',
            'direction',
            'status',
            'party',
            'purpose',
            'loaned_at',
            'due_at',
            'returned_at',
            'item_condition_out_id',
            'item_condition_in_id',
            'deposit_amount',
            'deposit_currency_code',
            'include_in_provenance',
            'created_at',
            'updated_at',
        ],
        'links' => ['self'],
    ];
});

it('lists the loans of a copy', function () {
    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);
    Loan::factory()->count(2)->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/loans')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => $this->jsonStructure], 'links', 'meta']);
});

it('does not list the loans of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyToLendForAccount($this->createAccount()->id);
    Loan::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/loans')->assertNotFound();
});

it('shows a loan', function () {
    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);
    $loan = Loan::factory()->create([
        'copy_id' => $copy->id,
        'direction' => LoanDirection::Outgoing,
        'status' => LoanStatus::Active,
        'party' => 'The Whitney Museum',
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/loans/'.$loan->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'loan')
        ->assertJsonPath('data.attributes.direction', 'outgoing')
        ->assertJsonPath('data.attributes.status', 'active')
        ->assertJsonPath('data.attributes.party', 'The Whitney Museum')
        ->assertJsonPath('data.links.self', route('api.copies.loans.show', [$copy->id, $loan->id]));
});

it('creates a loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/loans', [
        'direction' => 'outgoing',
        'party' => 'The Whitney Museum',
        'loaned_at' => '2024-01-01',
        'deposit_amount' => 250000,
        'deposit_currency_code' => 'USD',
    ])
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.direction', 'outgoing')
        ->assertJsonPath('data.attributes.status', 'active')
        ->assertJsonPath('data.attributes.deposit_amount', 250000);

    expect(Loan::query()->count())->toBe(1);
});

it('requires a direction, a party and a loaned date', function () {
    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/loans', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['direction', 'party', 'loaned_at']);
});

it('marks a loan as returned', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'status' => LoanStatus::Active]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/loans/'.$loan->id.'/return', [
        'returned_at' => '2024-06-01',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.status', 'returned');

    expect($loan->refresh()->status)->toBe(LoanStatus::Returned);
});

it('updates a loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'party' => 'Old party']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/loans/'.$loan->id, [
        'direction' => 'outgoing',
        'party' => 'The Tate',
        'loaned_at' => '2024-01-01',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.party', 'The Tate');

    expect($loan->refresh()->party)->toBe('The Tate');
});

it('deletes a loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyToLendForAccount($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/loans/'.$loan->id)->assertNoContent();

    $this->assertModelMissing($loan);
});

it('restricts writes to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyToLendForAccount($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/loans', [
        'direction' => 'outgoing',
        'party' => 'A gallery',
        'loaned_at' => '2024-01-01',
    ])->assertNotFound();
});
