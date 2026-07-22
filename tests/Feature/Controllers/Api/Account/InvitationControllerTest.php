<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'email',
            'role',
            'expires_at',
            'accepted_at',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the pending invitations of the account', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Viewer->value,
        'invited_by' => $owner->id,
    ]);
    Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'joey.tribbiani@friends.com',
        'role' => PermissionEnum::Editor->value,
        'invited_by' => $owner->id,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/invitations');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure,
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.type', 'invitation')
        ->assertJsonPath('data.0.attributes.email', 'phoebe.buffay@friends.com')
        ->assertJsonPath('data.0.attributes.role', PermissionEnum::Viewer->value)
        ->assertJsonPath('data.1.attributes.email', 'joey.tribbiani@friends.com')
        ->assertJsonPath('data.0.links.self', route('api.account.invitations'));
});

it('does not list invitations from another account', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->create([
        'email' => 'janice@friends.com',
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/invitations');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('does not list accepted invitations', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->accepted()->create([
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'invited_by' => $owner->id,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/invitations');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('does not list expired invitations', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->expired()->create([
        'account_id' => $account->id,
        'email' => 'joey.tribbiani@friends.com',
        'invited_by' => $owner->id,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/invitations');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('lists only the pending invitations among all of them', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'invited_by' => $owner->id,
    ]);
    Invitation::factory()->accepted()->create([
        'account_id' => $account->id,
        'email' => 'ross.geller@friends.com',
        'invited_by' => $owner->id,
    ]);
    Invitation::factory()->expired()->create([
        'account_id' => $account->id,
        'email' => 'joey.tribbiani@friends.com',
        'invited_by' => $owner->id,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/invitations');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.attributes.email', 'phoebe.buffay@friends.com');
});

it('restricts listing the invitations to owners', function (string $role) {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'invited_by' => $owner->id,
    ]);
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/account/invitations');

    $response->assertNotFound();
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('requires an authenticated user to list the invitations', function () {
    $response = $this->json('GET', '/api/account/invitations');

    $response->assertUnauthorized();
});
