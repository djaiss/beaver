<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Api\Vault\Adminland\AdminlandRelationshipTypeController;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandRelationshipTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'relationship_type_category_id',
                'key',
                'name',
                'forward_name',
                'reverse_name',
                'is_directed',
                'can_be_deleted',
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
    public function it_uses_the_create_controller_method(): void
    {
        $controller = AdminlandRelationshipTypeController::class;

        $this->assertSame(
            "{$controller}@create",
            Route::getRoutes()->getByName('api.vault.relationship_type.create')->getActionName(),
        );
    }

    #[Test]
    public function it_lists_relationship_types_of_a_vault(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory();
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            'name' => 'Second',
            'position' => 2,
        ]);
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            'name' => 'First',
            'position' => 1,
        ]);
        Sanctum::actingAs($user);

        $response = $this->json(
            'GET',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types",
        );

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['*' => $this->jsonStructure['data']]]);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.attributes.name', 'First');
        $response->assertJsonPath('data.1.attributes.name', 'Second');
    }

    #[Test]
    public function it_restricts_listing_relationship_types_to_vault_members(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        Sanctum::actingAs($user);

        $this->json(
            'GET',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types",
        )
            ->assertForbidden();
    }

    #[Test]
    public function it_can_show_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory();
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
        ]);
        Sanctum::actingAs($user);

        $this->json(
            'GET',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types/{$relationshipType->id}",
        )
            ->assertOk()
            ->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_returns_404_when_showing_a_relationship_type_from_another_vault(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory();
        $otherVault = Vault::factory()->create();
        $otherCategory = RelationshipTypeCategory::factory()->create(['vault_id' => $otherVault->id]);
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $otherVault->id,
            'relationship_type_category_id' => $otherCategory->id,
        ]);
        Sanctum::actingAs($user);

        $this->json(
            'GET',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types/{$relationshipType->id}",
        )
            ->assertNotFound();
    }

    #[Test]
    public function it_can_create_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory(PermissionEnum::Owner->value);
        Sanctum::actingAs($user);

        $this->json(
            'POST',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types",
            [
                'name' => 'Parent / child',
                'is_directed' => true,
                'forward_name' => 'Parent',
                'reverse_name' => 'Child',
            ],
        )
            ->assertCreated()
            ->assertJsonStructure($this->jsonStructure)
            ->assertJsonPath('data.attributes.name', 'Parent / child')
            ->assertJsonPath('data.attributes.forward_name', 'Parent')
            ->assertJsonPath('data.attributes.reverse_name', 'Child')
            ->assertJsonPath('data.attributes.position', 1);
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_create_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory();
        Sanctum::actingAs($user);

        $this->json(
            'POST',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types",
            [
                'name' => 'Friend',
            ],
        )->assertNotFound();
    }

    #[Test]
    public function it_can_update_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory(PermissionEnum::Owner->value);
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            'name' => 'Parent / child',
            'forward_name' => 'Parent',
            'reverse_name' => 'Child',
            'is_directed' => true,
            'position' => 1,
        ]);
        Sanctum::actingAs($user);

        $this->json(
            'PUT',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types/{$relationshipType->id}",
            [
                'name' => 'Guardian / ward',
                'is_directed' => true,
                'forward_name' => 'Guardian',
                'reverse_name' => 'Ward',
                'position' => 1,
            ],
        )
            ->assertOk()
            ->assertJsonStructure($this->jsonStructure)
            ->assertJsonPath('data.attributes.name', 'Guardian / ward')
            ->assertJsonPath('data.attributes.forward_name', 'Guardian')
            ->assertJsonPath('data.attributes.reverse_name', 'Ward');
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_update_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory();
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
        ]);
        Sanctum::actingAs($user);

        $this->json(
            'PUT',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types/{$relationshipType->id}",
            [
                'name' => 'Friend',
            ],
        )->assertNotFound();
    }

    #[Test]
    public function it_can_destroy_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory(PermissionEnum::Owner->value);
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            'can_be_deleted' => true,
        ]);
        Sanctum::actingAs($user);

        $this->json(
            'DELETE',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types/{$relationshipType->id}",
        )
            ->assertNoContent();
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_destroy_a_relationship_type(): void
    {
        [$user, $vault, $category] = $this->createMemberAndCategory();
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
        ]);
        Sanctum::actingAs($user);

        $this->json(
            'DELETE',
            "/api/vaults/{$vault->id}/relationship-type-categories/{$category->id}/relationship-types/{$relationshipType->id}",
        )
            ->assertNotFound();
    }

    private function createMemberAndCategory(string $role = PermissionEnum::Viewer->value): array
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, $role);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        return [$user, $vault, $category];
    }
}
