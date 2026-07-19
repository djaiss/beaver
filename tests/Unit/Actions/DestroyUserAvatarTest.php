<?php

declare(strict_types=1);
use App\Actions\DestroyUserAvatar;
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

it('removes the avatar and every one of its files', function () {
    $user = new UpdateUserAvatar(
        user: $this->user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    $path = $user->avatar_path;

    new DestroyUserAvatar(user: $user)->execute();

    expect($this->user->fresh()->avatar_path)->toBeNull();
    expect($this->user->fresh()->hasAvatar())->toBeFalse();

    Storage::assertMissing($path);

    foreach (User::avatarPixelSizes() as $pixels) {
        Storage::assertMissing(User::avatarVariantPathFor($path, $pixels));
    }

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::AvatarDeletion,
    );
});

it('does nothing when the user has no avatar', function () {
    new DestroyUserAvatar(user: $this->user)->execute();

    expect($this->user->fresh()->avatar_path)->toBeNull();

    Queue::assertNotPushed(LogUserAction::class);
});
