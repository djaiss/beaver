<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\PhotoViewEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('remembers the view for the acting user', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->put('/settings/photos/view', ['view' => PhotoViewEnum::ByItem->value])
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'photos_view' => PhotoViewEnum::ByItem->value,
    ]);
});

it('does not change what another member of the account sees', function () {
    $account = $this->createAccount();
    $monica = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Owner->value);
    $rachel = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Owner->value);

    $this->actingAs($monica)
        ->put('/settings/photos/view', ['view' => PhotoViewEnum::ByItem->value])
        ->assertNoContent();

    expect($rachel->fresh()->photos_view)->toBe(PhotoViewEnum::Grid);
});

it('rejects a view that does not exist', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->from('/settings/photos')
        ->put('/settings/photos/view', ['view' => 'carousel'])
        ->assertSessionHasErrors('view');

    expect($user->fresh()->photos_view)->toBe(PhotoViewEnum::Grid);
});

it('keeps a viewer out', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)
        ->put('/settings/photos/view', ['view' => PhotoViewEnum::ByItem->value])
        ->assertNotFound();
});

it('requires authentication', function () {
    $this->put('/settings/photos/view', ['view' => PhotoViewEnum::ByItem->value])->assertRedirect('/login');
});
