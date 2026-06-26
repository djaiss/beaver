<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\UpdatePerson;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdatePersonTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_person(): void
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
        ]);
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'first_name' => 'Old',
            'can_be_deleted' => true,
            'is_listed' => true,
        ]);

        $updatedPerson = new UpdatePerson(
            user: $user,
            person: $person,
            gender: $gender,
            firstName: '<strong>Regis</strong>',
            middleName: 'John',
            lastName: 'Smith',
            nickname: 'RJ',
            maidenName: 'Brown',
            suffix: 'Jr.',
            prefix: 'Mr.',
            kidsStatus: 'has_kids',
            canBeDeleted: false,
            isListed: false,
        )->execute();

        $this->assertSame('Regis', $updatedPerson->first_name);
        $this->assertSame("{$updatedPerson->id}-regis-smith", $updatedPerson->slug);
        $this->assertFalse($updatedPerson->can_be_deleted);
        $this->assertFalse($updatedPerson->is_listed);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::PersonUpdate
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

        new UpdatePerson(
            user: $user,
            person: $person,
            firstName: 'Regis',
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

        new UpdatePerson(
            user: $user,
            person: $person,
            firstName: 'Regis',
        )->execute();
    }

    #[Test]
    public function it_fails_if_gender_is_not_part_of_person_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = $this->createVault('Other vault');
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $person = Person::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $gender = Gender::factory()->create([
            'vault_id' => $otherVault->id,
        ]);

        new UpdatePerson(
            user: $user,
            person: $person,
            gender: $gender,
            firstName: 'Regis',
        )->execute();
    }
}
