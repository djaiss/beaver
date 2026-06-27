<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyGender;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyGenderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_gender(): void
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
            'name' => 'Other',
        ]);

        new DestroyGender(
            user: $user,
            gender: $gender,
        )->execute();

        $this->assertDatabaseMissing('genders', [
            'id' => $gender->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::GenderDeletion
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Other']
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

        new DestroyGender(
            user: $user,
            gender: $gender,
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

        new DestroyGender(
            user: $user,
            gender: $gender,
        )->execute();
    }
}
