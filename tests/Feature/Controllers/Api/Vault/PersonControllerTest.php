<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Vault;

use App\Enums\PermissionEnum;
use App\Models\Gender;
use App\Models\Person;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'gender_id',
                'kids_status',
                'slug',
                'first_name',
                'middle_name',
                'last_name',
                'nickname',
                'maiden_name',
                'suffix',
                'prefix',
                'can_be_deleted',
                'is_listed',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    #[Test]
    public function it_lists_persons_of_a_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        Person::factory()
            ->count(2)
            ->create([
                'vault_id' => $vault->id,
                'gender_id' => null,
            ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/vaults/{$vault->id}/persons");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure['data'],
            ],
        ]);
        $response->assertJsonCount(2, 'data');
    }

    #[Test]
    public function it_restricts_listing_persons_to_vault_members(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/vaults/{$vault->id}/persons");

        $response->assertForbidden();
    }

    #[Test]
    public function it_can_show_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'gender_id' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/vaults/{$vault->id}/persons/{$person->id}");

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_returns_404_when_showing_a_person_from_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = Vault::factory()->create();
        $this->assignUserToVault($user, $vault);
        $person = Person::factory()->create([
            'vault_id' => $otherVault->id,
            'gender_id' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/vaults/{$vault->id}/persons/{$person->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_create_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $gender = Gender::factory()->create([
            'vault_id' => $vault->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('POST', "/api/vaults/{$vault->id}/persons", [
            'gender_id' => $gender->id,
            'kids_status' => 'has_kids',
            'first_name' => 'Regis',
            'middle_name' => 'John',
            'last_name' => 'Smith',
            'nickname' => 'RJ',
            'maiden_name' => 'Brown',
            'suffix' => 'Jr.',
            'prefix' => 'Mr.',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure($this->jsonStructure);
        $response->assertJsonPath('data.attributes.first_name', 'Regis');
        $response->assertJsonPath('data.attributes.gender_id', (string) $gender->id);
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_create_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Viewer->value);

        Sanctum::actingAs($user);

        $response = $this->json('POST', "/api/vaults/{$vault->id}/persons", [
            'first_name' => 'Regis',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_update_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'gender_id' => null,
            'first_name' => 'Old',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/vaults/{$vault->id}/persons/{$person->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Person',
        ]);

        $response->assertOk();
        $response->assertJsonStructure($this->jsonStructure);
        $response->assertJsonPath('data.attributes.first_name', 'Updated');
        $response->assertJsonPath('data.attributes.last_name', 'Person');
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_update_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Viewer->value);
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'gender_id' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/vaults/{$vault->id}/persons/{$person->id}", [
            'first_name' => 'Updated',
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_can_destroy_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Editor->value);
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'gender_id' => null,
            'can_be_deleted' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', "/api/vaults/{$vault->id}/persons/{$person->id}");

        $response->assertNoContent();
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_destroy_a_person(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Viewer->value);
        $person = Person::factory()->create([
            'vault_id' => $vault->id,
            'gender_id' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', "/api/vaults/{$vault->id}/persons/{$person->id}");

        $response->assertNotFound();
    }
}
