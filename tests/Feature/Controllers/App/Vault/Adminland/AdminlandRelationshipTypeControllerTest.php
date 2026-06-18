<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Http\Controllers\App\Vault\Adminland\AdminlandRelationshipTypeController;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandRelationshipTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_uses_the_relationship_type_controller_for_form_routes(): void
    {
        $this->assertSame(
            AdminlandRelationshipTypeController::class.'@new',
            Route::getRoutes()->getByName('vault.adminland.relationship_types.new')->getActionName(),
        );
        $this->assertSame(
            AdminlandRelationshipTypeController::class.'@edit',
            Route::getRoutes()->getByName('vault.adminland.relationship_types.edit')->getActionName(),
        );
    }

    #[Test]
    public function it_shows_the_new_relationship_type_form(): void
    {
        [$user, $vault, $relationshipTypeCategory] = $this->createOwnerAndCategory();

        $response = $this->actingAs($user)
            ->get('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/new');

        $response->assertOk();
        $response->assertViewIs('app.vault.adminland._relationship-type-new');
        $response->assertViewHas('relationshipTypeCategory', $relationshipTypeCategory);
    }

    #[Test]
    public function it_creates_a_relationship_type_in_the_category(): void
    {
        Queue::fake();
        [$user, $vault, $relationshipTypeCategory] = $this->createOwnerAndCategory();

        $response = $this->actingAs($user)
            ->post('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types', [
                'name' => 'Parent',
                'is_directed' => '1',
            ]);

        $relationshipType = $relationshipTypeCategory->relationshipTypes()->sole();
        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Parent', $relationshipType->name);
        $this->assertTrue($relationshipType->is_directed);
        $this->assertSame(1, $relationshipType->position);
        $this->assertStringStartsWith('custom-', $relationshipType->key);
    }

    #[Test]
    public function it_shows_and_updates_a_relationship_type(): void
    {
        Queue::fake();
        [$user, $vault, $relationshipTypeCategory] = $this->createOwnerAndCategory();
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $relationshipTypeCategory->id,
            'name' => 'Parent',
            'is_directed' => true,
            'position' => 1,
        ]);

        $this->actingAs($user)
            ->get('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/'.$relationshipType->id.'/edit')
            ->assertOk()
            ->assertViewIs('app.vault.adminland._relationship-type-edit')
            ->assertSee('value="Parent"', false);

        $response = $this->actingAs($user)
            ->put('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/'.$relationshipType->id, [
                'name' => 'Guardian',
            ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Guardian', $relationshipType->refresh()->name);
        $this->assertFalse($relationshipType->is_directed);
        $this->assertSame(1, $relationshipType->position);
    }

    #[Test]
    public function it_deletes_a_relationship_type(): void
    {
        Queue::fake();
        [$user, $vault, $relationshipTypeCategory] = $this->createOwnerAndCategory();
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $relationshipTypeCategory->id,
            'position' => 1,
        ]);

        $response = $this->actingAs($user)
            ->delete('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/'.$relationshipType->id);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertModelMissing($relationshipType);
    }

    #[Test]
    public function it_rejects_a_relationship_type_from_another_category(): void
    {
        [$user, $vault, $relationshipTypeCategory] = $this->createOwnerAndCategory();
        $otherCategory = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $otherCategory->id,
        ]);

        $this->actingAs($user)
            ->put('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/'.$relationshipType->id, [
                'name' => 'Guardian',
            ])
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: Vault, 2: RelationshipTypeCategory}
     */
    private function createOwnerAndCategory(): array
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $relationshipTypeCategory = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'position' => 1,
        ]);

        return [$user, $vault, $relationshipTypeCategory];
    }
}
