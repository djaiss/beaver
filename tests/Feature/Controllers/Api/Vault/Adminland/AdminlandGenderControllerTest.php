<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Models\Gender;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandGenderControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'name',
                'position',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    #[Test]
    public function it_lists_the_genders_of_a_vault_in_position_order(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Second',
            'position' => 2,
        ]);
        Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'First',
            'position' => 1,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/genders');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure['data'],
            ],
        ]);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.attributes.name', 'First');
        $response->assertJsonPath('data.1.attributes.name', 'Second');
    }

    #[Test]
    public function it_does_not_list_genders_from_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = Vault::factory()->create();
        $this->assignUserToVault($user, $vault);
        Gender::factory()->create([
            'vault_id' => $otherVault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/genders');

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    #[Test]
    public function it_can_show_a_gender(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/genders/'.$gender->id);

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_returns_not_found_for_a_gender_from_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = Vault::factory()->create();
        $this->assignUserToVault($user, $vault);
        $gender = Gender::factory()->create([
            'vault_id' => $otherVault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/genders/'.$gender->id);

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_create_a_gender(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/vaults/'.$vault->id.'/genders', [
            'name' => 'Non-binary',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure($this->jsonStructure);
        $response->assertJsonPath('data.attributes.name', 'Non-binary');
        $response->assertJsonPath('data.attributes.position', 1);
    }

    #[Test]
    public function it_validates_the_name_when_creating_a_gender(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/vaults/'.$vault->id.'/genders', [
            'name' => 'No',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function it_restricts_gender_creation_to_the_vault_owner(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);

        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/vaults/'.$vault->id.'/genders', [
            'name' => 'Non-binary',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_update_a_gender(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Original',
            'position' => 3,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/vaults/'.$vault->id.'/genders/'.$gender->id, [
            'name' => 'Updated',
        ]);

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructure);
        $response->assertJsonPath('data.attributes.name', 'Updated');
        $response->assertJsonPath('data.attributes.position', 3);
    }

    #[Test]
    public function it_restricts_gender_updates_to_the_vault_owner(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/vaults/'.$vault->id.'/genders/'.$gender->id, [
            'name' => 'Updated',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_delete_a_gender(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/vaults/'.$vault->id.'/genders/'.$gender->id);

        $response->assertNoContent();
        $this->assertModelMissing($gender);
    }

    #[Test]
    public function it_restricts_gender_deletion_to_the_vault_owner(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/vaults/'.$vault->id.'/genders/'.$gender->id);

        $response->assertNotFound();
    }
}
