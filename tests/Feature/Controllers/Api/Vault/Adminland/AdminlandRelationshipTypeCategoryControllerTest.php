<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Api\Vault\Adminland\AdminlandRelationshipTypeCategoryController;
use App\Models\RelationshipTypeCategory;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandRelationshipTypeCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $jsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'key',
                'name',
                'position',
                'can_be_deleted',
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
        $this->assertSame(
            AdminlandRelationshipTypeCategoryController::class.'@create',
            Route::getRoutes()->getByName('api.vault.relationship_type_category.create')->getActionName(),
        );
    }

    #[Test]
    public function it_lists_relationship_type_categories_of_a_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id, 'name' => 'Second', 'position' => 2]);
        RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id, 'name' => 'First', 'position' => 1]);
        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/vaults/'.$vault->id.'/relationship-type-categories');

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['*' => $this->jsonStructure['data']]]);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.attributes.name', 'First');
        $response->assertJsonPath('data.1.attributes.name', 'Second');
    }

    #[Test]
    public function it_restricts_listing_relationship_type_categories_to_vault_members(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        Sanctum::actingAs($user);

        $this->json('GET', '/api/vaults/'.$vault->id.'/relationship-type-categories')
            ->assertForbidden();
    }

    #[Test]
    public function it_can_show_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        Sanctum::actingAs($user);

        $this->json('GET', '/api/vaults/'.$vault->id.'/relationship-type-categories/'.$category->id)
            ->assertOk()
            ->assertJsonStructure($this->jsonStructure);
    }

    #[Test]
    public function it_returns_404_when_showing_a_relationship_type_category_from_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = Vault::factory()->create();
        $this->assignUserToVault($user, $vault);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $otherVault->id]);
        Sanctum::actingAs($user);

        $this->json('GET', '/api/vaults/'.$vault->id.'/relationship-type-categories/'.$category->id)
            ->assertNotFound();
    }

    #[Test]
    public function it_can_create_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        Sanctum::actingAs($user);

        $this->json('POST', '/api/vaults/'.$vault->id.'/relationship-type-categories', [
            'name' => 'Extended family',
        ])
            ->assertCreated()
            ->assertJsonStructure($this->jsonStructure)
            ->assertJsonPath('data.attributes.name', 'Extended family')
            ->assertJsonPath('data.attributes.position', 1);
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_create_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        Sanctum::actingAs($user);

        $this->json('POST', '/api/vaults/'.$vault->id.'/relationship-type-categories', [
            'name' => 'Extended family',
        ])->assertNotFound();
    }

    #[Test]
    public function it_can_update_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Family',
            'position' => 1,
        ]);
        Sanctum::actingAs($user);

        $this->json('PUT', '/api/vaults/'.$vault->id.'/relationship-type-categories/'.$category->id, [
            'name' => 'Close family',
            'position' => 1,
        ])
            ->assertOk()
            ->assertJsonStructure($this->jsonStructure)
            ->assertJsonPath('data.attributes.name', 'Close family');
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_update_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        Sanctum::actingAs($user);

        $this->json('PUT', '/api/vaults/'.$vault->id.'/relationship-type-categories/'.$category->id, [
            'name' => 'Close family',
        ])->assertNotFound();
    }

    #[Test]
    public function it_can_destroy_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'can_be_deleted' => true,
        ]);
        Sanctum::actingAs($user);

        $this->json('DELETE', '/api/vaults/'.$vault->id.'/relationship-type-categories/'.$category->id)
            ->assertNoContent();
    }

    #[Test]
    public function it_returns_404_when_a_user_doesnt_have_permission_to_destroy_a_relationship_type_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        Sanctum::actingAs($user);

        $this->json('DELETE', '/api/vaults/'.$vault->id.'/relationship-type-categories/'.$category->id)
            ->assertNotFound();
    }
}
