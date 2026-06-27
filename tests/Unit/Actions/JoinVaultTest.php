<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\JoinVault;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JoinVaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_joins_a_vault(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = Vault::factory()->create([
            'invitation_code' => 'ABC123',
        ]);

        $result = new JoinVault(
            user: $user,
            invitationCode: 'ABC123',
        )->execute();

        $this->assertInstanceOf(Vault::class, $result);
        $this->assertEquals($vault->id, $result->id);

        $this->assertDatabaseHas('members', [
            'vault_id' => $vault->id,
            'user_id' => $user->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::VaultJoined
                && $job->user->id === $user->id
                && $job->vault->id === $vault->id
                && $job->parameters === ['name' => $vault->name]
            ),
        );
    }

    #[Test]
    public function it_rejects_an_invalid_invitation_code(): void
    {
        $user = $this->createUser();

        $this->expectException(ValidationException::class);

        new JoinVault(
            user: $user,
            invitationCode: 'INVALID',
        )->execute();
    }

    #[Test]
    public function it_rejects_if_user_is_already_a_member(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value
        );
        $vault->update(['invitation_code' => 'ABC123']);

        $this->expectException(ValidationException::class);

        new JoinVault(
            user: $user,
            invitationCode: 'ABC123',
        )->execute();
    }
}
