<?php

declare(strict_types=1);
use App\Actions\UpdateUserPassword;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates user password', function () {
    Queue::fake();

    $user = $this->createUser([
        'password' => Hash::make('current-password'),
    ]);

    $updatedUser = new UpdateUserPassword(
        user: $user,
        currentPassword: 'current-password',
        newPassword: 'new-password',
    )->execute();

    expect(Hash::check('new-password', $updatedUser->fresh()->password))->toBeTrue();

    expect($updatedUser)->toBeInstanceOf(User::class);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::UpdateUserPassword
            && $job->user->id === $user->id
        ),
    );
});

it('throws exception when current password is incorrect', function () {
    $user = User::factory()->create([
        'password' => Hash::make('current-password'),
    ]);

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Current password is incorrect');

    new UpdateUserPassword(
        user: $user,
        currentPassword: Hash::make('wrong-password'),
        newPassword: 'new-password',
    )->execute();
});
