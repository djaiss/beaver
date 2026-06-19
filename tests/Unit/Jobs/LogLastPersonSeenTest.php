<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\PermissionEnum;
use App\Jobs\LogLastPersonSeen;
use App\Models\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogLastPersonSeenTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_logs_the_last_person_seen_by_a_user(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $member = $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
        ]);

        LogLastPersonSeen::dispatch($user, $person);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'last_person_seen_id' => $person->id,
        ]);
    }
}
