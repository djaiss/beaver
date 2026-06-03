<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateVault;
use App\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateVaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_an_organization(): void
    {
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
            name: 'Threat Level Midnight',
        )->execute();

        $this->assertEquals('Threat Level Midnight', $updatedVault->name);
        $this->assertEquals($vault->id.'-threat-level-midnight', $updatedVault->slug);
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
            name: 'Dunder@ / Mifflin!',
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
            role: PermissionEnum::Viewer->value
        );

        new UpdateVault(
            user: $user,
            vault: $vault,
            name: 'Valid Vault Name',
        )->execute();
    }
}
