<?php

declare(strict_types=1);
use App\Jobs\DeleteInactiveUsers;
use App\Mail\UserAutomaticallyDeleted;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('deletes users inactive for six months', function () {
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
});

it('does not delete users inactive for less than six months', function () {
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
});

it('does not delete users without auto delete enabled', function () {
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
});

it('does not delete users with null last activity', function () {
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
});
