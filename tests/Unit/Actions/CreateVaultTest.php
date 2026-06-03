<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateVault;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\PopulateVault;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateVaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_vault(): void
    {
        Queue::fake();

        $user = $this->createUser();

        $vault = new CreateVault(
            user: $user,
            name: 'Dunder Mifflin',
        )->execute();

        $expectedSlug = $vault->id . '-dunder-mifflin';

        $this->assertInstanceOf(Vault::class, $vault);

        $this->assertDatabaseHas('vaults', [
            'id' => $vault->id,
            'name' => 'Dunder Mifflin',
            'slug' => $expectedSlug,
        ]);

        $this->assertDatabaseHas('members', [
            'vault_id' => $vault->id,
            'user_id' => $user->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn(LogUserAction $job): bool => (
                $job->action === UserActionEnum::VaultCreation
                && $job->user->id === $user->id
                && $job->description === 'Created a vault called Dunder Mifflin'
            ),
        );
    }

    #[Test]
    public function it_rejects_vault_names_with_special_characters(): void
    {
        $user = $this->createUser();

        $this->expectException(ValidationException::class);

        new CreateVault(
            user: $user,
            name: 'Dunder & Mifflin',
        )->execute();
    }

    #[Test]
    public function it_rejects_vault_names_with_reserved_keywords(): void
    {
        config(['app.reserved_vault_keywords' => ['admin', 'support', 'contact']]);
        $user = $this->createUser();

        $this->expectException(ValidationException::class);

        new CreateVault(
            user: $user,
            name: 'Admin',
        )->execute();
    }
}
