<?php

declare(strict_types=1);
use App\Actions\CreateApiKeyForLogin;
use App\Enums\EmailType;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\NewLoginDetected;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a token logs the sign in and notifies the user', function () {
    Queue::fake();

    $user = User::factory()->create();

    $token = new CreateApiKeyForLogin(
        user: $user,
        deviceName: 'Rachel iPhone 15',
    )->execute();

    expect($token)->toBeString();
    expect($token)->not->toBeEmpty();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
        'name' => 'Login from Rachel iPhone 15',
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::ApiKeyCreation
            && $job->user->id === $user->id
        ),
    );

    Queue::assertPushedOn(
        queue: 'high',
        job: SendEmail::class,
        callback: fn (SendEmail $job): bool => (
            $job->mailable instanceof NewLoginDetected
            && $job->mailable->device === 'Rachel iPhone 15'
            && $job->user->id === $user->id
            && $job->emailType === EmailType::NewLogin
        ),
    );
});
it('falls back to an unknown device label', function () {
    Queue::fake();

    $user = User::factory()->create();

    new CreateApiKeyForLogin(
        user: $user,
        deviceName: null,
    )->execute();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'Login from an unknown device',
    ]);

    Queue::assertPushed(
        SendEmail::class,
        fn (SendEmail $job): bool => (
            $job->mailable instanceof NewLoginDetected
            && $job->mailable->device === 'an unknown device'
        ),
    );
});
