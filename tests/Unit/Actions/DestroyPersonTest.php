<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyPerson;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyPersonTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_person(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Regis',
            'can_be_deleted' => true,
        ]);

        new DestroyPerson(
            user: $user,
            person: $person,
        )->execute();

        $this->assertDatabaseMissing('persons', [
            'id' => $person->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::PersonDeletion
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Regis']
            ),
        );
    }

    #[Test]
    public function it_fails_if_user_is_not_part_of_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
        ]);

        new DestroyPerson(
            user: $user,
            person: $person,
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

        $person = Person::factory()->create([
            'vault_id' => $vault->id,
        ]);

        new DestroyPerson(
            user: $user,
            person: $person,
        )->execute();
    }

    #[Test]
    public function it_fails_if_person_cannot_be_deleted(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'can_be_deleted' => false,
        ]);

        new DestroyPerson(
            user: $user,
            person: $person,
        )->execute();
    }
}
