<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_adminland(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland');

        $response->assertStatus(200);
    }

    #[Test]
    public function it_restricts_adminland(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value,
        );

        $response = $this->actingAs($user)->get('/vaults/'.$vault->id.'/adminland');

        $response->assertStatus(403);
    }

    #[Test]
    public function it_updates_the_vault(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->put('/vaults/'.$vault->id.'/adminland', [
            'vault_name' => 'Central Perk',
        ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Central Perk', $vault->refresh()->name);
    }

    #[Test]
    public function it_validates_the_vault_name_when_updating_the_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->from('/vaults/'.$vault->id.'/adminland')->put('/vaults/'.$vault->id.'/adminland', [
            'vault_name' => 'Central@ / Perk!',
        ]);

        $response->assertRedirect('/vaults/'.$vault->id.'/adminland');
        $response->assertSessionHasErrors('vault_name');
        $this->assertSame('New York Public Library', $vault->refresh()->name);
    }
}
