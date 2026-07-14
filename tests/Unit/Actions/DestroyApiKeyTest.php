<?php

declare(strict_types=1);
use App\Actions\DestroyApiKey;
use App\Enums\EmailType;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\ApiKeyDestroyed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes an api key', function () {
    Queue::fake();

    $user = User::factory()->create();
    $user->createToken('Test API Key');

    $tokenId = $user->tokens()->first()->id;

    new DestroyApiKey(
        user: $user,
        tokenId: $tokenId,
    )->execute();

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::ApiKeyDeletion
            && $job->user->id === $user->id
        ),
    );

    Queue::assertPushedOn(
        queue: 'high',
        job: SendEmail::class,
        callback: fn (SendEmail $job): bool => (
            $job->mailable instanceof ApiKeyDestroyed
            && $job->mailable->label === 'Test API Key'
            && $job->user->id === $user->id
            && $job->emailType === EmailType::ApiDestroyed
        ),
    );
});
