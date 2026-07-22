<?php

declare(strict_types=1);
use App\Actions\UpdateUserPhotoView;
use App\Enums\PhotoViewEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('stores the chosen view on the user', function () {
    $user = $this->createUser();

    $result = new UpdateUserPhotoView(
        user: $user,
        view: PhotoViewEnum::ByItem->value,
    )->execute();

    expect($result)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'photos_view' => PhotoViewEnum::ByItem->value,
    ]);
});

it('defaults to the grid before anything is chosen', function () {
    expect($this->createUser()->photos_view)->toBe(PhotoViewEnum::Grid);
});

it('leaves the other members of the account alone', function () {
    $account = $this->createAccount();
    $monica = $this->assignUserToAccount(user: $this->createUser(), account: $account);
    $rachel = $this->assignUserToAccount(user: $this->createUser(), account: $account);

    new UpdateUserPhotoView(user: $monica, view: PhotoViewEnum::ByItem->value)->execute();

    expect($monica->fresh()->photos_view)->toBe(PhotoViewEnum::ByItem);
    expect($rachel->fresh()->photos_view)->toBe(PhotoViewEnum::Grid);
});

it('refuses a view that does not exist', function () {
    $user = $this->createUser();

    expect(fn () => new UpdateUserPhotoView(user: $user, view: 'carousel')->execute())
        ->toThrow(ValidationException::class);
});
