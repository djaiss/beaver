<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreatePerson;
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

class CreatePersonTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_person(): void
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

        $person = new CreatePerson(
            user: $user,
            vault: $vault,
            gender: $gender,
            firstName: '<strong>Regis</strong>',
            middleName: 'John',
            lastName: 'Smith',
            nickname: 'RJ',
            maidenName: 'Brown',
            suffix: 'Jr.',
            prefix: 'Mr.',
            maritalStatus: 'married',
            kidsStatus: 'has_kids',
            canBeDeleted: false,
            isListed: false,
        )->execute();

        $this->assertInstanceOf(Person::class, $person);
        $this->assertSame('Regis', $person->first_name);
        $this->assertSame($person->id.'-regis-smith', $person->slug);
        $this->assertDatabaseHas('persons', [
            'id' => $person->id,
            'vault_id' => $vault->id,
            'gender_id' => $gender->id,
            'can_be_deleted' => false,
            'is_listed' => false,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::PersonCreation
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

        new CreatePerson(
            user: $user,
            vault: $vault,
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

        new CreatePerson(
            user: $user,
            vault: $vault,
            firstName: 'Regis',
        )->execute();
    }

    #[Test]
    public function it_fails_if_gender_is_not_part_of_vault(): void
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

        $gender = Gender::factory()->create([
            'vault_id' => $otherVault->id,
        ]);

        new CreatePerson(
            user: $user,
            vault: $vault,
            gender: $gender,
            firstName: 'Regis',
        )->execute();
    }
}
