<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateMaritalStatus;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\MaritalStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateMaritalStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_marital_status(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'position' => 2,
        ]);

        $updatedMaritalStatus = new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus,
            name: 'Man',
        )->execute();

        $this->assertInstanceOf(MaritalStatus::class, $updatedMaritalStatus);
        $this->assertEquals('Man', $updatedMaritalStatus->name);
        $this->assertEquals(2, $updatedMaritalStatus->position);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::MaritalStatusUpdate
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Man']
            ),
        );
    }

    #[Test]
    public function it_updates_a_marital_status_with_a_null_name(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'name_translation_key' => 'app/shared.marital_statuses.married',
            'position' => 2,
        ]);

        $updatedMaritalStatus = new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus,
            name: null,
        )->execute();

        $this->assertInstanceOf(MaritalStatus::class, $updatedMaritalStatus);
        $this->assertNull($updatedMaritalStatus->name);
        $this->assertEquals('app/shared.marital_statuses.married', $updatedMaritalStatus->name_translation_key);
        $this->assertEquals(2, $updatedMaritalStatus->position);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::MaritalStatusUpdate
                && $job->user->id === $user->id
                && $job->parameters === ['name' => null]
            ),
        );
    }

    #[Test]
    public function it_fails_if_user_is_not_part_of_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
        ]);

        new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus,
            name: 'Updated',
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

        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
        ]);

        new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus,
            name: 'Updated',
        )->execute();
    }

    #[Test]
    public function it_updates_position_when_provided(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        // Create multiple marital_statuses so position 3 is valid
        MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 2]);
        MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 3]);

        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'position' => 1,
        ]);

        $updatedMaritalStatus = new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus,
            name: 'Man',
            position: 3,
        )->execute();

        $this->assertEquals('Man', $updatedMaritalStatus->name);
        $this->assertEquals(3, $updatedMaritalStatus->position);
    }

    #[Test]
    public function it_reorders_positions_when_moving_down(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        // Create marital_statuses at positions 1, 2, 3, 4, 5
        $maritalStatus1 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 1, 'name' => 'MaritalStatus 1']);
        $maritalStatus2 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 2, 'name' => 'MaritalStatus 2']);
        $maritalStatus3 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 3, 'name' => 'MaritalStatus 3']);
        $maritalStatus4 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 4, 'name' => 'MaritalStatus 4']);
        $maritalStatus5 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 5, 'name' => 'MaritalStatus 5']);

        // Move maritalStatus2 from position 2 to position 4
        new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus2,
            name: 'MaritalStatus 2',
            position: 4,
        )->execute();

        // Refresh all models
        $maritalStatus1->refresh();
        $maritalStatus2->refresh();
        $maritalStatus3->refresh();
        $maritalStatus4->refresh();
        $maritalStatus5->refresh();

        // Expected: 1, 4, 2, 3, 5 (maritalStatus3 and maritalStatus4 moved up)
        $this->assertEquals(1, $maritalStatus1->position);
        $this->assertEquals(4, $maritalStatus2->position);
        $this->assertEquals(2, $maritalStatus3->position);
        $this->assertEquals(3, $maritalStatus4->position);
        $this->assertEquals(5, $maritalStatus5->position);
    }

    #[Test]
    public function it_reorders_positions_when_moving_up(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        // Create marital_statuses at positions 1, 2, 3, 4, 5
        $maritalStatus1 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 1, 'name' => 'MaritalStatus 1']);
        $maritalStatus2 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 2, 'name' => 'MaritalStatus 2']);
        $maritalStatus3 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 3, 'name' => 'MaritalStatus 3']);
        $maritalStatus4 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 4, 'name' => 'MaritalStatus 4']);
        $maritalStatus5 = MaritalStatus::factory()->create(['vault_id' => $vault->id, 'position' => 5, 'name' => 'MaritalStatus 5']);

        // Move maritalStatus4 from position 4 to position 2
        new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus4,
            name: 'MaritalStatus 4',
            position: 2,
        )->execute();

        // Refresh all models
        $maritalStatus1->refresh();
        $maritalStatus2->refresh();
        $maritalStatus3->refresh();
        $maritalStatus4->refresh();
        $maritalStatus5->refresh();

        // Expected: 1, 3, 4, 2, 5 (maritalStatus2 and maritalStatus3 moved down)
        $this->assertEquals(1, $maritalStatus1->position);
        $this->assertEquals(3, $maritalStatus2->position);
        $this->assertEquals(4, $maritalStatus3->position);
        $this->assertEquals(2, $maritalStatus4->position);
        $this->assertEquals(5, $maritalStatus5->position);
    }

    #[Test]
    public function it_does_not_update_position_when_not_provided(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 3,
        ]);

        $updatedMaritalStatus = new UpdateMaritalStatus(
            user: $user,
            maritalStatus: $maritalStatus,
            name: 'Woman',
        )->execute();

        $this->assertEquals('Woman', $updatedMaritalStatus->name);
        $this->assertEquals(3, $updatedMaritalStatus->position);
    }
}
