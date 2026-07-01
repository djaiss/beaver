<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateSpecialDate;
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

class UpdateSpecialDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_special_date(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $person = Person::factory()->create(['vault_id' => $vault->id]);
        $specialDate = SpecialDate::factory()->create([
            'vault_id' => $vault->id,
            'person_id' => $person->id,
            'name' => 'Birthday',
        ]);
        Queue::fake();

        $updatedSpecialDate = new UpdateSpecialDate(
            user: $user,
            specialDate: $specialDate,
            name: ' <em>Wedding anniversary</em> ',
            shouldBeReminded: true,
            isApproximate: true,
            year: 2020,
            month: 6,
            day: 20,
        )->execute();

        $this->assertSame($specialDate->id, $updatedSpecialDate->id);
        $this->assertSame('Wedding anniversary', $updatedSpecialDate->name);
        $this->assertTrue($updatedSpecialDate->should_be_reminded);
        $this->assertTrue($updatedSpecialDate->is_approximate);
        $this->assertSame(2020, $updatedSpecialDate->year);
        $this->assertSame(6, $updatedSpecialDate->month);
        $this->assertSame(20, $updatedSpecialDate->day);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::SpecialDateUpdate
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Wedding anniversary']
            ),
        );
    }

    #[Test]
    public function it_fails_if_the_user_is_not_part_of_the_vault(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        new UpdateSpecialDate($user, $specialDate, 'Birthday', false)->execute();
    }

    #[Test]
    public function it_fails_if_the_user_is_a_viewer(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create();
        $this->assignUserToVault($user, $specialDate->vault, PermissionEnum::Viewer->value);

        $this->expectException(ModelNotFoundException::class);

        new UpdateSpecialDate($user, $specialDate, 'Birthday', false)->execute();
    }

    #[Test]
    public function it_fails_if_the_person_belongs_to_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $person = Person::factory()->create();
        $specialDate = SpecialDate::factory()->create([
            'vault_id' => $vault->id,
            'person_id' => $person->id,
        ]);
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);

        $this->expectException(ModelNotFoundException::class);

        new UpdateSpecialDate($user, $specialDate, 'Birthday', false)->execute();
    }

    #[Test]
    public function it_fails_for_an_invalid_calendar_date(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create();
        $this->assignUserToVault($user, $specialDate->vault, PermissionEnum::Editor->value);

        $this->expectException(ModelNotFoundException::class);

        new UpdateSpecialDate(
            user: $user,
            specialDate: $specialDate,
            name: 'Birthday',
            shouldBeReminded: false,
            month: 13,
        )->execute();
    }

    #[Test]
    public function it_fails_when_no_date_part_is_provided(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create();
        $this->assignUserToVault($user, $specialDate->vault, PermissionEnum::Editor->value);

        $this->expectException(ModelNotFoundException::class);

        new UpdateSpecialDate(
            user: $user,
            specialDate: $specialDate,
            name: 'Birthday',
            shouldBeReminded: false,
        )->execute();
    }
}
