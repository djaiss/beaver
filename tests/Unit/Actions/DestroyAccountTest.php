<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyAccount;
use App\Mail\AccountDestroyed;
use App\Models\AccountDeletionReason;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyAccountTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_destroys_an_account(): void
    {
        Queue::fake();
        Mail::fake();
        config(['app.account_deletion_notification_email' => 'regis@lifeos.com']);

        $user = User::factory()->create();

        new DestroyAccount(
            user: $user,
            reason: 'the service is not working',
        )->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $this->assertEquals(
            1,
            AccountDeletionReason::query()->count(),
        );

        Mail::assertQueued(AccountDestroyed::class, fn(AccountDestroyed $job): bool => $job->reason === 'the service is not working'
            && $job->to[0]['address'] === 'regis@lifeos.com');
    }
}
