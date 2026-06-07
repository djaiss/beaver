<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateMaritalStatus;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\MaritalStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateMaritalStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_marital_status(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $maritalStatus = new CreateMaritalStatus(
            user: $user,
            vault: $vault,
            name: 'Non-binary',
        )->execute();

        $this->assertInstanceOf(MaritalStatus::class, $maritalStatus);
        $this->assertDatabaseHas('marital_statuses', [
            'id' => $maritalStatus->id,
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::MaritalStatusCreation
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Non-binary']
            ),
        );
    }

    #[Test]
    public function it_increments_position_automatically(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'position' => 5,
        ]);

        $maritalStatus = new CreateMaritalStatus(
            user: $user,
            vault: $vault,
            name: 'Other',
        )->execute();

        $this->assertEquals(6, $maritalStatus->position);
    }

    #[Test]
    public function it_fails_if_user_is_not_part_of_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();

        new CreateMaritalStatus(
            user: $user,
            vault: $vault,
            name: 'Male',
        )->execute();
    }

    #[Test]
    public function it_fails_if_user_is_only_viewer(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value,
        );

        new CreateMaritalStatus(
            user: $user,
            vault: $vault,
            name: 'Female',
        )->execute();
    }
}
