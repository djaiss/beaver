<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteInactiveUsers;
use App\Mail\UserAutomaticallyDeleted;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteInactiveUsersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_users_inactive_for_six_months(): void
    {
        config(['app.account_deletion_notification_email' => 'admin@example.com']);
        Mail::fake();

        $user = User::factory()->create([
            'auto_delete_user' => true,
            'last_activity_at' => now()->subMonths(6),
            'created_at' => now()->subMonths(12),
        ]);

        $job = new DeleteInactiveUsers;
        $job->handle();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        Mail::assertQueued(
            UserAutomaticallyDeleted::class,
            fn (UserAutomaticallyDeleted $mail): bool => $mail->hasTo('admin@example.com'),
        );
    }

    #[Test]
    public function it_does_not_delete_users_inactive_for_less_than_six_months(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'auto_delete_user' => true,
            'last_activity_at' => now()->subMonths(5),
        ]);

        $job = new DeleteInactiveUsers;
        $job->handle();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        Mail::assertNotQueued(UserAutomaticallyDeleted::class);
    }

    #[Test]
    public function it_does_not_delete_users_without_auto_delete_enabled(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'auto_delete_user' => false,
            'last_activity_at' => now()->subMonths(6),
        ]);

        $job = new DeleteInactiveUsers;
        $job->handle();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        Mail::assertNotQueued(UserAutomaticallyDeleted::class);
    }

    #[Test]
    public function it_does_not_delete_users_with_null_last_activity(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'auto_delete_user' => true,
            'last_activity_at' => null,
        ]);

        $job = new DeleteInactiveUsers;
        $job->handle();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        Mail::assertNotQueued(UserAutomaticallyDeleted::class);
    }
}
