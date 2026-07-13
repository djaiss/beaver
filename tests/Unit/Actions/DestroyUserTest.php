<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyUser;
use App\Mail\UserDeleted;
use App\Models\User;
use App\Models\UserDeletionReason;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyUserTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_destroys_an_account(): void
    {
        Queue::fake();
        Mail::fake();
        config(['app.account_deletion_notification_email' => 'regis@beaver.com']);

        $user = User::factory()->create();

        new DestroyUser(
            user: $user,
            reason: 'the service is not working',
        )->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $this->assertEquals(
            1,
            UserDeletionReason::query()->count(),
        );

        Mail::assertQueued(UserDeleted::class, fn (UserDeleted $job): bool => $job->reason === 'the service is not working'
            && $job->to[0]['address'] === 'regis@beaver.com');
    }
}
