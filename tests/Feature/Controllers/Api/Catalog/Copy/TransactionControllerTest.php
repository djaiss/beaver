<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\TransactionType;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create a copy belonging to the given account.
 */
function copyForAccount(int $accountId): Copy
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
            'type',
            'counterparty',
            'amount',
            'currency_code',
            'tax_amount',
            'fee_amount',
            'shipping_amount',
            'total_amount',
            'total',
            'occurred_at',
            'reference_number',
            'source_url',
            'note',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the transactions of a copy', function () {
    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);
    Transaction::factory()->create(['copy_id' => $copy->id]);
    Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/transactions')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list the transactions of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForAccount($this->createAccount()->id);
    Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/transactions')->assertNotFound();
});

it('shows a transaction', function () {
    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'counterparty' => 'Central Perk Collectibles',
        'amount' => 5000,
        'currency_code' => 'USD',
        'total_amount' => 6550,
        'note' => 'Bought while Ross was on a break.',
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'transaction')
        ->assertJsonPath('data.id', (string) $transaction->id)
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.type', 'purchase')
        ->assertJsonPath('data.attributes.counterparty', 'Central Perk Collectibles')
        ->assertJsonPath('data.attributes.amount', 5000)
        ->assertJsonPath('data.attributes.total_amount', 6550)
        ->assertJsonPath('data.attributes.total', 6550)
        ->assertJsonPath('data.attributes.note', 'Bought while Ross was on a break.')
        ->assertJsonPath('data.links.self', route('api.copies.transactions.show', [$copy->id, $transaction->id]));
});

it('derives the total from the parts when none was recorded', function () {
    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 5000,
        'tax_amount' => 400,
        'fee_amount' => 250,
        'shipping_amount' => 900,
        'total_amount' => null,
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.total_amount', null)
        ->assertJsonPath('data.attributes.total', 6550);
});

it('does not show a transaction of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForAccount($this->createAccount()->id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id)->assertNotFound();
});

it('creates a transaction', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/copies/'.$copy->id.'/transactions', [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2024-01-15',
        'counterparty' => 'Gunther',
        'amount' => 5000,
        'currency_code' => 'USD',
        'tax_amount' => 400,
        'reference_number' => 'INV-1994',
        'note' => 'Pivot!',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.type', 'purchase')
        ->assertJsonPath('data.attributes.counterparty', 'Gunther')
        ->assertJsonPath('data.attributes.amount', 5000)
        ->assertJsonPath('data.attributes.total', 5400);

    $transaction = Transaction::query()->latest('id')->first();
    expect($transaction->copy_id)->toBe($copy->id);
    expect($transaction->type)->toBe(TransactionType::Purchase);
    expect($transaction->occurred_at->toDateString())->toBe('2024-01-15');
});

it('validates the type when creating a transaction', function () {
    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/transactions', [
        'type' => 'smelly-cat',
        'occurred_at' => '2024-01-15',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('requires a type and a date when creating a transaction', function () {
    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/transactions', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'occurred_at']);
});

it('validates the amount when creating a transaction', function () {
    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/transactions', [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2024-01-15',
        'amount' => -1,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['amount']);
});

it('does not create a transaction on a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForAccount($this->createAccount()->id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/transactions', [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2024-01-15',
    ])->assertNotFound();
});

it('restricts transaction creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyForAccount($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/transactions', [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2024-01-15',
    ])->assertNotFound();
});

it('updates a transaction', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'amount' => 5000,
    ]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id, [
        'type' => TransactionType::Sale->value,
        'occurred_at' => '2024-06-15',
        'counterparty' => 'Phoebe Buffay',
        'amount' => 9900,
        'total_amount' => 9900,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.type', 'sale')
        ->assertJsonPath('data.attributes.counterparty', 'Phoebe Buffay')
        ->assertJsonPath('data.attributes.amount', 9900)
        ->assertJsonPath('data.attributes.total', 9900);

    $transaction->refresh();
    expect($transaction->type)->toBe(TransactionType::Sale);
    expect($transaction->amount)->toBe(9900);
    expect($transaction->occurred_at->toDateString())->toBe('2024-06-15');
});

it('restricts transaction updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyForAccount($account->id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id, [
        'type' => TransactionType::Sale->value,
        'occurred_at' => '2024-06-15',
    ])->assertNotFound();
});

it('deletes a transaction', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForAccount($user->account_id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id)->assertNoContent();

    $this->assertModelMissing($transaction);
});

it('restricts transaction deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyForAccount($account->id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/transactions/'.$transaction->id)->assertNotFound();
});
