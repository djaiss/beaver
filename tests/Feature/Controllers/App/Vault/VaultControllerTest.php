<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault;

use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Enums\PermissionEnum;

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

        $response->assertStatus(200);
        $response->assertViewHas(
            'vaults',
            fn ($vaults): bool => $vaults->count() === 1
            && $vaults->every(fn ($vault): bool => isset($vault->name, $vault->link, $vault->avatar)),
        );
    }

    #[Test]
    public function it_shows_the_create_vault_page(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/vaults/create');

        $response->assertStatus(200);
        $response->assertViewIs('app.vault.create');
    }

    #[Test]
    public function it_creates_a_vault(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->post('/vaults', [
            'vault_name' => 'Super Library',
        ]);

        $vault = Vault::query()->where('name', 'Super Library')->first();
        $response->assertRedirect('/vaults/'.$vault->slug);
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
            ->get('/vaults/'.$vault->slug);

        $response->assertStatus(200);
        $response->assertViewIs('app.vault.show');
        $response->assertViewHas('vault');
    }
}
