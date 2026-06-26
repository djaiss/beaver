<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyAccount;
use App\Enums\PermissionEnum;
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

        Mail::assertQueued(
            AccountDestroyed::class,
            fn (AccountDestroyed $job): bool => (
                $job->reason === 'the service is not working'
                && $job->to[0]['address'] === 'regis@lifeos.com'
            ),
        );
    }

    #[Test]
    public function it_deletes_vaults_where_user_is_the_only_owner(): void
    {
        Queue::fake();
        Mail::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        new DestroyAccount(
            user: $user,
            reason: 'test reason',
        )->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseMissing('vaults', [
            'id' => $vault->id,
        ]);
    }

    #[Test]
    public function it_does_not_delete_vaults_with_multiple_owners(): void
    {
        Queue::fake();
        Mail::fake();

        $user = $this->createUser();
        $otherUser = $this->createUser();
        $vault = $this->createVault();

        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $this->assignUserToVault(
            user: $otherUser,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        new DestroyAccount(
            user: $user,
            reason: 'test reason',
        )->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        // Vault should still exist because there's another owner
        $this->assertDatabaseHas('vaults', [
            'id' => $vault->id,
        ]);
    }

    #[Test]
    public function it_does_not_delete_vaults_where_user_is_not_owner(): void
    {
        Queue::fake();
        Mail::fake();

        $user = $this->createUser();
        $owner = $this->createUser();
        $vault = $this->createVault();

        $this->assignUserToVault(
            user: $owner,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Editor->value,
        );

        new DestroyAccount(
            user: $user,
            reason: 'test reason',
        )->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        // Vault should still exist because user was not an owner
        $this->assertDatabaseHas('vaults', [
            'id' => $vault->id,
        ]);
    }
}
