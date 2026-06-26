<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateVault;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateVaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_vault(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $updatedVault = new UpdateVault(
            user: $user,
            vault: $vault,
            name: 'Joey Tribbiani Special',
        )->execute();

        $this->assertEquals('Joey Tribbiani Special', $updatedVault->name);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::VaultUpdate
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Joey Tribbiani Special']
            ),
        );
    }

    #[Test]
    public function it_throws_an_exception_if_name_contains_special_characters(): void
    {
        $this->expectException(ValidationException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        new UpdateVault(
            user: $user,
            vault: $vault,
            name: 'Central@ / Perk!',
        )->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_vault_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $otherVault = $this->createVault(name: 'Other Vault');

        new UpdateVault(
            user: $user,
            vault: $otherVault,
            name: 'Valid Vault Name',
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

        new UpdateVault(
            user: $user,
            vault: $vault,
            name: 'Valid Vault Name',
        )->execute();
    }
}
