<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyVault;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyVaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_vault(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        new DestroyVault(
            user: $user,
            vault: $vault,
        )->execute();

        $this->assertDatabaseMissing('vaults', [
            'id' => $vault->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::VaultDeletion
                && $job->user->id === $user->id
                && $job->parameters === ['name' => $vault->name]
            ),
        );
    }

    #[Test]
    public function it_throws_an_exception_if_vault_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherVault = Vault::factory()->create();

        new DestroyVault(
            user: $user,
            vault: $otherVault,
        )->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_member_doesnt_have_right_permission(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value,
        );

        new DestroyVault(
            user: $user,
            vault: $vault,
        )->execute();
    }
}
