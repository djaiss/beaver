<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api;

use App\Enums\PermissionEnum;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VaultControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'avatar',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    #[Test]
    public function it_lists_the_vaults_of_the_current_user(): void
    {
        $user = $this->createUser();
        $vault = Vault::factory()->create();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
        );
        $vault = Vault::factory()->create();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
        );

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/vaults');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure['data'],
            ],
        ]);
        $response->assertJsonCount(2, 'data');
    }

    #[Test]
    public function it_can_create_a_new_vault(): void
    {
        Date::setTestNow('2026-02-24 14:19:37');
        $user = $this->createUser();

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/vaults', [
            'name' => 'Central Perk',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_can_show_a_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
        );

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/vaults/{$vault->id}");

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_restricts_access_to_a_vault(): void
    {
        $user = $this->createUser();
        $vault = Vault::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/vaults/{$vault->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function it_can_update_the_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/vaults/{$vault->id}", [
            'name' => 'Central Perk Chandler Edition',
        ]);

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_can_delete_a_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', "/api/vaults/{$vault->id}");

        $response->assertNoContent();
    }
}
