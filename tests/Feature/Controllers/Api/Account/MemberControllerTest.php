<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Mail\AccountInvitation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'first_name',
            'last_name',
            'nickname',
            'email',
            'role',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];

    $this->invitationStructure = [
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

it('lists the members of the account', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser([
        'first_name' => 'Monica',
        'last_name' => 'Geller',
    ]), $account, PermissionEnum::Owner->value);
    $this->assignUserToAccount($this->createUser([
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
    ]), $account, PermissionEnum::Editor->value);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/members');

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
        ->assertJsonPath('data.0.attributes.first_name', 'Monica')
        ->assertJsonPath('data.1.attributes.first_name', 'Chandler')
        ->assertJsonPath('data.1.attributes.role', PermissionEnum::Editor->value);
});

it('does not list members from another account', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    User::factory()->create([
        'first_name' => 'Janice',
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/members');

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', (string) $owner->id);
});

it('shows a member', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $member = $this->assignUserToAccount($this->createUser([
        'first_name' => 'Phoebe',
        'last_name' => 'Buffay',
        'email' => 'phoebe.buffay@friends.com',
    ]), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/members/'.$member->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'member')
        ->assertJsonPath('data.id', (string) $member->id)
        ->assertJsonPath('data.attributes.first_name', 'Phoebe')
        ->assertJsonPath('data.attributes.email', 'phoebe.buffay@friends.com')
        ->assertJsonPath('data.attributes.role', PermissionEnum::Viewer->value)
        ->assertJsonPath('data.links.self', route('api.account.members.show', $member->id));
});

it('returns not found for a member from another account', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $stranger = User::factory()->create();

    Sanctum::actingAs($owner);

    $response = $this->json('GET', '/api/account/members/'.$stranger->id);

    $response->assertNotFound();
});

it('restricts listing the members to owners', function (string $role) {
    $account = $this->createAccount();
    $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/account/members');

    $response->assertNotFound();
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('restricts showing a member to owners', function (string $role) {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/account/members/'.$owner->id);

    $response->assertNotFound();
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('invites a person to the account', function () {
    Queue::fake();
    Mail::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('POST', '/api/account/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Editor->value,
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->invitationStructure,
        ])
        ->assertJsonPath('data.type', 'invitation')
        ->assertJsonPath('data.attributes.email', 'phoebe.buffay@friends.com')
        ->assertJsonPath('data.attributes.role', PermissionEnum::Editor->value)
        ->assertJsonPath('data.attributes.accepted_at', null);

    $this->assertDatabaseHas('invitations', [
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Editor->value,
        'invited_by' => $owner->id,
    ]);

    Mail::assertQueued(AccountInvitation::class);
});

it('does not create a member when inviting a person', function () {
    Queue::fake();
    Mail::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $this->json('POST', '/api/account/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Viewer->value,
    ])->assertCreated();

    expect($account->users()->count())->toBe(1);
    $this->assertDatabaseMissing('users', [
        'email' => 'phoebe.buffay@friends.com',
    ]);
});

it('validates the fields when inviting a person', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('POST', '/api/account/members', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'role']);
});

it('validates the role when inviting a person', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('POST', '/api/account/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => 'lobster',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role']);
});

it('rejects an invitation for someone who is already a member', function () {
    Queue::fake();
    Mail::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $this->assignUserToAccount($this->createUser([
        'email' => 'phoebe.buffay@friends.com',
    ]), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($owner);

    $response = $this->json('POST', '/api/account/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects an invitation for someone already invited', function () {
    Queue::fake();
    Mail::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'invited_by' => $owner->id,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('POST', '/api/account/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('restricts inviting a person to owners', function (string $role) {
    Queue::fake();
    Mail::fake();

    $account = $this->createAccount();
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/account/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response->assertNotFound();

    $this->assertDatabaseMissing('invitations', [
        'email' => 'phoebe.buffay@friends.com',
    ]);
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('updates the role of a member', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $member = $this->assignUserToAccount($this->createUser([
        'first_name' => 'Joey',
    ]), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($owner);

    $response = $this->json('PUT', '/api/account/members/'.$member->id, [
        'role' => PermissionEnum::Editor->value,
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.id', (string) $member->id)
        ->assertJsonPath('data.attributes.role', PermissionEnum::Editor->value);

    expect($member->refresh()->role)->toBe(PermissionEnum::Editor->value);
});

it('validates the role when updating a member', function () {
    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $member = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($owner);

    $response = $this->json('PUT', '/api/account/members/'.$member->id, [
        'role' => 'lobster',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role']);
});

it('refuses to demote the last owner', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('PUT', '/api/account/members/'.$owner->id, [
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['role']);

    expect($owner->refresh()->role)->toBe(PermissionEnum::Owner->value);
});

it('demotes an owner when another owner remains', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $otherOwner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('PUT', '/api/account/members/'.$otherOwner->id, [
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.attributes.role', PermissionEnum::Viewer->value);

    expect($otherOwner->refresh()->role)->toBe(PermissionEnum::Viewer->value);
});

it('returns not found when updating a member from another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $stranger = User::factory()->create([
        'role' => PermissionEnum::Viewer->value,
    ]);

    Sanctum::actingAs($owner);

    $response = $this->json('PUT', '/api/account/members/'.$stranger->id, [
        'role' => PermissionEnum::Editor->value,
    ]);

    $response->assertNotFound();

    expect($stranger->refresh()->role)->toBe(PermissionEnum::Viewer->value);
});

it('restricts updating a member role to owners', function (string $role) {
    Queue::fake();

    $account = $this->createAccount();
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);
    $member = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/account/members/'.$member->id, [
        'role' => PermissionEnum::Editor->value,
    ]);

    $response->assertNotFound();

    expect($member->refresh()->role)->toBe(PermissionEnum::Viewer->value);
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);

it('removes a member', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $member = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($owner);

    $response = $this->json('DELETE', '/api/account/members/'.$member->id);

    $response->assertNoContent();

    $this->assertModelMissing($member);
    $this->assertModelExists($owner);
});

it('refuses to remove the last owner', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('DELETE', '/api/account/members/'.$owner->id);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['member']);

    $this->assertModelExists($owner);
});

it('removes yourself when another owner remains', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $otherOwner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);

    Sanctum::actingAs($owner);

    $response = $this->json('DELETE', '/api/account/members/'.$owner->id);

    $response->assertNoContent();

    $this->assertModelMissing($owner);
    $this->assertModelExists($otherOwner);
});

it('returns not found when removing a member from another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Owner->value);
    $stranger = User::factory()->create();

    Sanctum::actingAs($owner);

    $response = $this->json('DELETE', '/api/account/members/'.$stranger->id);

    $response->assertNotFound();

    $this->assertModelExists($stranger);
});

it('restricts removing a member to owners', function (string $role) {
    Queue::fake();

    $account = $this->createAccount();
    $user = $this->assignUserToAccount($this->createUser(), $account, $role);
    $member = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/account/members/'.$member->id);

    $response->assertNotFound();

    $this->assertModelExists($member);
})->with([
    PermissionEnum::Editor->value,
    PermissionEnum::Viewer->value,
]);
