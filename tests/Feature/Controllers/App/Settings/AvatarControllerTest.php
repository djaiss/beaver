<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Storage::fake();

    $this->account = $this->createAccount();
    $this->user = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $this->user, account: $this->account);
});

it('uploads an avatar', function () {
    $response = $this->actingAs($this->user)
        ->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('ross.jpg', 400, 400),
        ]);

    $response->assertRedirect(route('profile.index'));
    $response->assertSessionHas('status', 'Changes saved');

    $this->user->refresh();

    expect($this->user->hasAvatar())->toBeTrue();
    Storage::assertExists($this->user->avatar_path);
});

it('rejects a file that is not an image', function () {
    $response = $this->actingAs($this->user)
        ->post(route('profile.avatar.update'), [
            'avatar' => UploadedFile::fake()->create('ross.pdf', 10, 'application/pdf'),
        ]);

    $response->assertSessionHasErrors('avatar');

    expect($this->user->fresh()->hasAvatar())->toBeFalse();
});

it('removes an avatar', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $response = $this->actingAs($this->user)
        ->delete(route('profile.avatar.destroy'));

    $response->assertRedirect(route('profile.index'));

    expect($this->user->fresh()->hasAvatar())->toBeFalse();
});

it('streams a resized avatar to a member of the same account', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $rachel = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $this->assignUserToAccount(user: $rachel, account: $this->account);

    $response = $this->actingAs($rachel)
        ->get(route('profile.avatar.show', ['user' => $this->user, 'size' => 64]));

    $response->assertOk();
});

it('does not serve the avatar of a user of another account', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $otherAccount = $this->createAccount();
    $janice = $this->createUser(['first_name' => 'Janice', 'last_name' => 'Hosenstein']);
    $this->assignUserToAccount(user: $janice, account: $otherAccount);

    $response = $this->actingAs($janice)
        ->get(route('profile.avatar.show', ['user' => $this->user, 'size' => 64]));

    $response->assertNotFound();
});

it('does not serve a size it never generated', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $response = $this->actingAs($this->user)
        ->get(route('profile.avatar.show', ['user' => $this->user, 'size' => 999]));

    $response->assertNotFound();
});

it('returns not found when the user has no avatar', function () {
    $response = $this->actingAs($this->user)
        ->get(route('profile.avatar.show', ['user' => $this->user, 'size' => 64]));

    $response->assertNotFound();
});

it('shows the avatar section on the profile page', function () {
    $response = $this->actingAs($this->user)->get(route('profile.index'));

    $response->assertOk();
    $response->assertSee('Avatar');
});

it('renders the avatar image with both densities once one is uploaded', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $response = $this->actingAs($this->user->fresh())->get(route('profile.index'));

    $response->assertOk();
    $response->assertSee('rounded-full object-cover', escape: false);
    $response->assertSee(route('profile.avatar.show', ['user' => $this->user, 'size' => 192]), escape: false);
    $response->assertSee('Remove avatar');
});

it('falls back to the initials when the user has no avatar', function () {
    $response = $this->actingAs($this->user)->get(route('profile.index'));

    $response->assertOk();
    $response->assertSee('RG');
    $response->assertDontSee('Remove avatar');
});

it('exposes only the sizes it writes to disk', function () {
    expect(User::avatarPixelSizes())->toBe([32, 64, 96, 128, 192]);
});
