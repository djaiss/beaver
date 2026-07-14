<?php

declare(strict_types=1);
use App\Actions\CreateApiKey;
use App\Enums\EmailType;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\ApiKeyCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates an api key', function () {
    Queue::fake();

    $user = User::factory()->create();

    $token = new CreateApiKey(
        user: $user,
        label: 'Production API Key',
    )->execute();

    expect($token)->toBeString();
    expect($token)->not->toBeEmpty();

    $this->assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
        'name' => 'Production API Key',
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
            $job->mailable instanceof ApiKeyCreated
            && $job->mailable->label === 'Production API Key'
            && $job->user->id === $user->id
            && $job->emailType === EmailType::ApiCreated
        ),
    );
});
