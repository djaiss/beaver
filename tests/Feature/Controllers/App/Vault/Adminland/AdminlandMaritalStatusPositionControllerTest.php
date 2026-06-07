<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Models\MaritalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandMaritalStatusPositionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_marital_status_position(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $firstMaritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Female',
            'position' => 1,
        ]);
        $secondMaritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Male',
            'position' => 2,
        ]);
        $thirdMaritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Other',
            'position' => 3,
        ]);

        $response = $this->actingAs($user)->put('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$firstMaritalStatus->id.'/position', [
            'position' => '3',
        ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame(3, $firstMaritalStatus->refresh()->position);
        $this->assertSame(1, $secondMaritalStatus->refresh()->position);
        $this->assertSame(2, $thirdMaritalStatus->refresh()->position);
    }

    #[Test]
    public function it_validates_the_position(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)
            ->from('/vaults/'.$vault->id.'/adminland')
            ->put('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$maritalStatus->id.'/position', [
                'position' => 0,
            ]);

        $response->assertRedirect('/vaults/'.$vault->id.'/adminland');
        $response->assertSessionHasErrors('position');
        $this->assertSame(1, $maritalStatus->refresh()->position);
    }

    #[Test]
    public function it_restricts_marital_status_position_updates(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value,
        );
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->put('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$maritalStatus->id.'/position', [
            'position' => 2,
        ]);

        $response->assertStatus(403);
        $this->assertSame(1, $maritalStatus->refresh()->position);
    }

    #[Test]
    public function it_rejects_a_marital_status_from_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = $this->createVault(name: 'Other Vault');
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );
        $maritalStatus = MaritalStatus::factory()->create([
            'vault_id' => $otherVault->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->put('/vaults/'.$vault->id.'/adminland/marital-statuses/'.$maritalStatus->id.'/position', [
            'position' => 1,
        ]);

        $response->assertStatus(404);
    }
}
