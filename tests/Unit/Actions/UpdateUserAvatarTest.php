<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
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

it('stores the avatar, renames the file and logs the action', function () {
    $user = new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    expect($user)->toBeInstanceOf(User::class);
    expect($user->avatar_path)->toStartWith('avatars/'.$this->user->id.'/');
    expect($user->avatar_path)->not->toContain('ross');

    Storage::assertExists($user->avatar_path);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'avatar_path' => $user->avatar_path,
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::AvatarUpdate,
    );
});

it('writes a square version at every size, and at twice each size', function () {
    $user = new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    foreach (User::avatarPixelSizes() as $pixels) {
        $path = $user->avatarVariantPath($pixels);

        Storage::assertExists($path);

        $size = getimagesizefromstring(Storage::get($path));

        expect($size[0])->toBe($pixels);
        expect($size[1])->toBe($pixels);
    }

    // The doubled versions are what a dense screen picks from the srcset.
    expect(User::avatarPixelSizes())->toContain(64, 128, 192);
});

it('crops a rectangular image to a square rather than squashing it', function () {
    $user = new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 800, 200),
    )->execute();

    $size = getimagesizefromstring(Storage::get($user->avatarVariantPath(64)));

    expect($size[0])->toBe(64);
    expect($size[1])->toBe(64);
});

it('removes the files of the previous avatar', function () {
    $first = new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $firstPath = $first->avatar_path;

    $second = new UpdateUserAvatar(
        user: $this->user->fresh(),
        file: UploadedFile::fake()->image('rachel.jpg', 400, 400),
    )->execute();

    expect($second->avatar_path)->not->toBe($firstPath);

    Storage::assertMissing($firstPath);
    Storage::assertMissing(User::avatarVariantPathFor($firstPath, 64));
    Storage::assertExists($second->avatar_path);
});

it('rejects a file that is not an image we accept', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->create('ross.pdf', 10, 'application/pdf'),
    )->execute();
})->throws(InvalidArgumentException::class);

it('rejects a file larger than 5 MB', function () {
    new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg')->size(6 * 1024),
    )->execute();
})->throws(InvalidArgumentException::class);
