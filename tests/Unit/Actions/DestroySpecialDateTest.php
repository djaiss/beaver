<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroySpecialDate;
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

class DestroySpecialDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_special_date(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create(['name' => 'Birthday']);
        $this->assignUserToVault($user, $specialDate->vault, PermissionEnum::Editor->value);
        Queue::fake();

        new DestroySpecialDate($user, $specialDate)->execute();

        $this->assertModelMissing($specialDate);
        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::SpecialDateDeletion
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Birthday']
            ),
        );
    }

    #[Test]
    public function it_fails_if_the_user_is_not_part_of_the_vault(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create();

        $this->expectException(ModelNotFoundException::class);

        new DestroySpecialDate($user, $specialDate)->execute();
    }

    #[Test]
    public function it_fails_if_the_user_is_a_viewer(): void
    {
        $user = $this->createUser();
        $specialDate = SpecialDate::factory()->create();
        $this->assignUserToVault($user, $specialDate->vault, PermissionEnum::Viewer->value);

        $this->expectException(ModelNotFoundException::class);

        new DestroySpecialDate($user, $specialDate)->execute();
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

        new DestroySpecialDate($user, $specialDate)->execute();
    }
}
