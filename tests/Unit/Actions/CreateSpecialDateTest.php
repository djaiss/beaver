<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateSpecialDate;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Person;
use App\Models\SpecialDate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateSpecialDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_special_date(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $person = Person::factory()->create(['vault_id' => $vault->id]);
        Queue::fake();

        $specialDate = new CreateSpecialDate(
            user: $user,
            vault: $vault,
            person: $person,
            name: ' <strong>Birthday</strong> ',
            shouldBeReminded: true,
            year: 1990,
            month: 2,
            day: 14,
        )->execute();

        $this->assertInstanceOf(SpecialDate::class, $specialDate);
        $this->assertModelExists($specialDate);
        $this->assertSame($vault->id, $specialDate->vault_id);
        $this->assertSame($person->id, $specialDate->person_id);
        $this->assertSame('Birthday', $specialDate->name);
        $this->assertTrue($specialDate->should_be_reminded);
        $this->assertSame(1990, $specialDate->year);
        $this->assertSame(2, $specialDate->month);
        $this->assertSame(14, $specialDate->day);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::SpecialDateCreation
                && $job->user->id === $user->id
                && $job->vault->id === $vault->id
                && $job->parameters === ['name' => 'Birthday']
            ),
        );
    }

    #[Test]
    public function it_fails_if_the_user_is_not_part_of_the_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $person = Person::factory()->create(['vault_id' => $vault->id]);

        $this->expectException(ModelNotFoundException::class);

        new CreateSpecialDate($user, $vault, $person, 'Birthday')->execute();
    }

    #[Test]
    public function it_fails_if_the_user_is_a_viewer(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Viewer->value);
        $person = Person::factory()->create(['vault_id' => $vault->id]);

        $this->expectException(ModelNotFoundException::class);

        new CreateSpecialDate($user, $vault, $person, 'Birthday')->execute();
    }

    #[Test]
    public function it_fails_if_the_person_belongs_to_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $person = Person::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        new CreateSpecialDate($user, $vault, $person, 'Birthday')->execute();
    }

    #[Test]
    public function it_fails_for_an_invalid_calendar_date(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $person = Person::factory()->create(['vault_id' => $vault->id]);

        $this->expectException(ModelNotFoundException::class);

        new CreateSpecialDate(
            user: $user,
            vault: $vault,
            person: $person,
            name: 'Birthday',
            year: 2025,
            month: 2,
            day: 29,
        )->execute();
    }
}
