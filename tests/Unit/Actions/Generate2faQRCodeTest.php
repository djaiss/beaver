<?php

declare(strict_types=1);
use App\Actions\Generate2faQRCode;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('generates a 2fa qr code', function () {
    Queue::fake();

    Date::setTestNow(Date::parse('2025-07-16 10:00:00'));

    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $result = new Generate2faQRCode(
        user: $user,
    )->execute();

    expect($result['secret'])->toBeString();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::TwoFaQrCodeGeneration
            && $job->user->id === $user->id
        ),
    );
});
