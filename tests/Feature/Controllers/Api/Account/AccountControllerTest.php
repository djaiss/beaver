<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'currency_code',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('shows the account of the user', function () {
    $account = $this->createAccount('Central Perk');
    $account->update(['currency_code' => 'USD']);
    $user = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/account');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'account')
        ->assertJsonPath('data.id', (string) $account->id)
        ->assertJsonPath('data.attributes.name', 'Central Perk')
        ->assertJsonPath('data.attributes.currency_code', 'USD')
        ->assertJsonPath('data.links.self', route('api.account'));
});

it('shows the account to a viewer', function () {
    $account = $this->createAccount('Central Perk');
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $response = $this->json('GET', '/api/account');

    $response
        ->assertOk()
        ->assertJsonPath('data.id', (string) $account->id);
});

it('updates the account', function () {
    Queue::fake();

    $account = $this->createAccount('Central Perk');
    $user = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/account', [
        'name' => 'Monica Apartment',
        'currency_code' => 'EUR',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Monica Apartment')
        ->assertJsonPath('data.attributes.currency_code', 'EUR');

    $account->refresh();
    expect($account->name)->toBe('Monica Apartment');
    expect($account->currency_code)->toBe('EUR');
});

it('validates the fields when updating the account', function () {
    $account = $this->createAccount();
    $user = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/account', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'currency_code']);
});

it('validates the currency code when updating the account', function () {
    $account = $this->createAccount();
    $user = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/account', [
        'name' => 'Central Perk',
        'currency_code' => 'GALLEON',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['currency_code']);
});

it('restricts the account update to owners', function (string $role) {
    Queue::fake();

    $account = $this->createAccount('Central Perk');
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/account', [
        'name' => 'Monica Apartment',
        'currency_code' => 'EUR',
    ]);

    $response->assertNotFound();

    expect($account->refresh()->name)->toBe('Central Perk');
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('deletes the account and everyone in it', function () {
    Queue::fake();

    $account = $this->createAccount('Central Perk');
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($owner);

    $response = $this->json('DELETE', '/api/account');

    $response->assertNoContent();

    $this->assertModelMissing($account);
    $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    expect(Account::query()->find($account->id))->toBeNull();

    /*
     * The members go with the account through the cascading foreign key on
     * users.account_id. SQLite keeps foreign keys off inside the transaction
     * RefreshDatabase opens, so the cascade cannot be observed here.
     */
    expect(User::query()->find($owner->id)?->account()->exists())->not->toBeTrue();
});

it('restricts the account deletion to owners', function (string $role) {
    Queue::fake();

    $account = $this->createAccount();
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/account');

    $response->assertNotFound();

    $this->assertModelExists($account);
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('does not touch another account', function () {
    Queue::fake();

    $account = $this->createAccount('Central Perk');
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $otherAccount = Account::factory()->create([
        'name' => 'Moondance Diner',
    ]);

    Sanctum::actingAs($owner);

    $this->json('PUT', '/api/account', [
        'name' => 'Monica Apartment',
        'currency_code' => 'EUR',
    ])->assertOk();

    expect($otherAccount->refresh()->name)->toBe('Moondance Diner');
});
