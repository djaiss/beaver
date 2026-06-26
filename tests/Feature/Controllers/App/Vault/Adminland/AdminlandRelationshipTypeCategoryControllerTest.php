<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Http\Controllers\App\Vault\Adminland\AdminlandRelationshipTypeCategoryController;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandRelationshipTypeCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_uses_the_relationship_type_category_controller_for_form_routes(): void
    {
        $controller = AdminlandRelationshipTypeCategoryController::class;

        $this->assertSame(
            "{$controller}@new",
            Route::getRoutes()->getByName('vault.adminland.relationship_type_categories.new')->getActionName(),
        );
        $this->assertSame(
            "{$controller}@edit",
            Route::getRoutes()->getByName('vault.adminland.relationship_type_categories.edit')->getActionName(),
        );
    }

    #[Test]
    public function it_shows_the_new_relationship_type_category_form(): void
    {
        [$user, $vault] = $this->createOwnerAndVault();

        $response = $this->actingAs($user)
            ->get("/vaults/{$vault->id}/adminland/relationship-type-categories/new");

        $response->assertOk();
        $response->assertViewIs('app.vault.adminland._relationship-type-category-new');
        $response->assertViewHas('vault', $vault);
    }

    #[Test]
    public function it_creates_a_relationship_type_category(): void
    {
        Queue::fake();
        [$user, $vault] = $this->createOwnerAndVault();

        $response = $this->actingAs($user)
            ->post("/vaults/{$vault->id}/adminland/relationship-type-categories", [
                'name' => 'Extended family',
            ]);

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->sole();
        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Extended family', $relationshipTypeCategory->name);
        $this->assertSame(1, $relationshipTypeCategory->position);
        $this->assertStringStartsWith('custom-', $relationshipTypeCategory->key);
    }

    #[Test]
    public function it_shows_and_updates_a_relationship_type_category(): void
    {
        Queue::fake();
        [$user, $vault] = $this->createOwnerAndVault();
        $relationshipTypeCategory = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Family',
            'position' => 1,
        ]);

        $this->actingAs($user)
            ->get(
                "/vaults/{$vault->id}/adminland/relationship-type-categories/{$relationshipTypeCategory->id}/edit",
            )
            ->assertOk()
            ->assertViewIs('app.vault.adminland._relationship-type-category-edit')
            ->assertSee('value="Family"', false);

        $response = $this->actingAs($user)
            ->put("/vaults/{$vault->id}/adminland/relationship-type-categories/{$relationshipTypeCategory->id}", [
                'name' => 'Close family',
            ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Close family', $relationshipTypeCategory->refresh()->name);
        $this->assertSame(1, $relationshipTypeCategory->position);
    }

    #[Test]
    public function it_deletes_a_relationship_type_category_and_its_types(): void
    {
        Queue::fake();
        [$user, $vault] = $this->createOwnerAndVault();
        $relationshipTypeCategory = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $relationshipTypeCategory->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)
            ->delete("/vaults/{$vault->id}/adminland/relationship-type-categories/{$relationshipTypeCategory->id}");

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertModelMissing($relationshipTypeCategory);
        $this->assertModelMissing($relationshipType);
    }

    #[Test]
    public function it_rejects_a_relationship_type_category_from_another_vault(): void
    {
        [$user, $vault] = $this->createOwnerAndVault();
        $relationshipTypeCategory = RelationshipTypeCategory::factory()->create();

        $this->actingAs($user)
            ->put("/vaults/{$vault->id}/adminland/relationship-type-categories/{$relationshipTypeCategory->id}", [
                'name' => 'Close family',
            ])
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: Vault}
     */
    private function createOwnerAndVault(): array
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);

        return [$user, $vault];
    }
}
