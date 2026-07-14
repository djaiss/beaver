<?php

declare(strict_types=1);
use App\Actions\Remove2fa;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('removes 2fa from user account', function () {
    Queue::fake();

    $user = User::factory()->create([
        'two_factor_secret' => 'test-secret',
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => ['code1', 'code2'],
    ]);

    new Remove2fa(
        user: $user,
    )->execute();

    $user->refresh();

    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::TwoFaRemoval
            && $job->user->id === $user->id
        ),
    );
});
