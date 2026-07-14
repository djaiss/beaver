<?php

declare(strict_types=1);
use App\Actions\ToggleAutoDeleteUser;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('enables auto delete user', function () {
    Queue::fake();

    $user = User::factory()->create([
        'auto_delete_user' => false,
    ]);

    $updatedUser = new ToggleAutoDeleteUser(
        user: $user,
        autoDeleteUser: true,
    )->execute();

    expect($updatedUser)->toBeInstanceOf(User::class);
    expect($updatedUser->auto_delete_user)->toBeTrue();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::AutoDeleteUserUpdate
            && $job->user->id === $user->id
            && $job->parameters === ['status' => 'enabled']
        ),
    );
});
