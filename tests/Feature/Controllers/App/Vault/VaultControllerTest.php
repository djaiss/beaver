<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault;

use App\Enums\PermissionEnum;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VaultControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_list_of_vaults(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get('/vaults');

        $response->assertOk();
    }

    #[Test]
    public function it_shows_the_create_vault_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/vaults/create');

        $response->assertOk();
    }

    #[Test]
    public function it_creates_a_vault(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/vaults', [
            'vault_name' => 'Super Library',
        ]);

        $vault = Vault::query()->latest()->first();
        $response->assertRedirect("/vaults/{$vault->id}");
        $response->assertSessionHas('status', 'Vault created successfully');
    }

    #[Test]
    public function it_shows_a_single_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)
            ->get("/vaults/{$vault->id}");

        $response->assertOk();
        $response->assertViewIs('app.vault.show');
        $response->assertViewHas('vault');
    }
}
