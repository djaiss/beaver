<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateGender;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateGenderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_gender(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'position' => 2,
        ]);

        $updatedGender = new UpdateGender(
            user: $user,
            gender: $gender,
            name: 'Man',
        )->execute();

        $this->assertInstanceOf(Gender::class, $updatedGender);
        $this->assertEquals('Man', $updatedGender->name);
        $this->assertEquals(2, $updatedGender->position);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::GenderUpdate
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Man']
            ),
        );
    }

    #[Test]
    public function it_updates_a_gender_with_a_null_name(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'name_translation_key' => 'app/shared.genders.man',
            'position' => 2,
        ]);

        $updatedGender = new UpdateGender(
            user: $user,
            gender: $gender,
            name: null,
        )->execute();

        $this->assertInstanceOf(Gender::class, $updatedGender);
        $this->assertNull($updatedGender->name);
        $this->assertEquals('app/shared.genders.man', $updatedGender->name_translation_key);
        $this->assertEquals(2, $updatedGender->position);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::GenderUpdate
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
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        new UpdateGender(
            user: $user,
            gender: $gender,
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

        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        new UpdateGender(
            user: $user,
            gender: $gender,
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

        // Create multiple genders so position 3 is valid
        Gender::factory()->create(['vault_id' => $vault->id, 'position' => 2]);
        Gender::factory()->create(['vault_id' => $vault->id, 'position' => 3]);

        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'position' => 1,
        ]);

        $updatedGender = new UpdateGender(
            user: $user,
            gender: $gender,
            name: 'Man',
            position: 3,
        )->execute();

        $this->assertEquals('Man', $updatedGender->name);
        $this->assertEquals(3, $updatedGender->position);
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

        // Create genders at positions 1, 2, 3, 4, 5
        $gender1 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 1, 'name' => 'Gender 1']);
        $gender2 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 2, 'name' => 'Gender 2']);
        $gender3 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 3, 'name' => 'Gender 3']);
        $gender4 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 4, 'name' => 'Gender 4']);
        $gender5 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 5, 'name' => 'Gender 5']);

        // Move gender2 from position 2 to position 4
        new UpdateGender(
            user: $user,
            gender: $gender2,
            name: 'Gender 2',
            position: 4,
        )->execute();

        // Refresh all models
        $gender1->refresh();
        $gender2->refresh();
        $gender3->refresh();
        $gender4->refresh();
        $gender5->refresh();

        // Expected: 1, 4, 2, 3, 5 (gender3 and gender4 moved up)
        $this->assertEquals(1, $gender1->position);
        $this->assertEquals(4, $gender2->position);
        $this->assertEquals(2, $gender3->position);
        $this->assertEquals(3, $gender4->position);
        $this->assertEquals(5, $gender5->position);
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

        // Create genders at positions 1, 2, 3, 4, 5
        $gender1 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 1, 'name' => 'Gender 1']);
        $gender2 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 2, 'name' => 'Gender 2']);
        $gender3 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 3, 'name' => 'Gender 3']);
        $gender4 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 4, 'name' => 'Gender 4']);
        $gender5 = Gender::factory()->create(['vault_id' => $vault->id, 'position' => 5, 'name' => 'Gender 5']);

        // Move gender4 from position 4 to position 2
        new UpdateGender(
            user: $user,
            gender: $gender4,
            name: 'Gender 4',
            position: 2,
        )->execute();

        // Refresh all models
        $gender1->refresh();
        $gender2->refresh();
        $gender3->refresh();
        $gender4->refresh();
        $gender5->refresh();

        // Expected: 1, 3, 4, 2, 5 (gender2 and gender3 moved down)
        $this->assertEquals(1, $gender1->position);
        $this->assertEquals(3, $gender2->position);
        $this->assertEquals(4, $gender3->position);
        $this->assertEquals(2, $gender4->position);
        $this->assertEquals(5, $gender5->position);
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

        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 3,
        ]);

        $updatedGender = new UpdateGender(
            user: $user,
            gender: $gender,
            name: 'Woman',
        )->execute();

        $this->assertEquals('Woman', $updatedGender->name);
        $this->assertEquals(3, $updatedGender->position);
    }
}
