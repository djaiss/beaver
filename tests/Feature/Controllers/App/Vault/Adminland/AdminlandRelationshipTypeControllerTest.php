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
                'name' => 'Parent / child',
                'is_directed' => '1',
                'forward_name' => 'Parent',
                'reverse_name' => 'Child',
            ]);

        $relationshipType = $relationshipTypeCategory->relationshipTypes()->sole();
        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Parent / child', $relationshipType->name);
        $this->assertSame('Parent', $relationshipType->forward_name);
        $this->assertSame('Child', $relationshipType->reverse_name);
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
            'name' => 'Parent / child',
            'forward_name' => 'Parent',
            'reverse_name' => 'Child',
            'is_directed' => true,
            'position' => 1,
        ]);

        $this->actingAs($user)
            ->get('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/'.$relationshipType->id.'/edit')
            ->assertOk()
            ->assertViewIs('app.vault.adminland._relationship-type-edit')
            ->assertSee('value="Parent / child"', false)
            ->assertSee('value="Parent"', false)
            ->assertSee('value="Child"', false);

        $response = $this->actingAs($user)
            ->put('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types/'.$relationshipType->id, [
                'name' => 'Guardian / ward',
                'is_directed' => '1',
                'forward_name' => 'Guardian',
                'reverse_name' => 'Ward',
            ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Guardian / ward', $relationshipType->refresh()->name);
        $this->assertSame('Guardian', $relationshipType->forward_name);
        $this->assertSame('Ward', $relationshipType->reverse_name);
        $this->assertTrue($relationshipType->is_directed);
        $this->assertSame(1, $relationshipType->position);
    }

    #[Test]
    public function it_requires_forward_and_reverse_names_for_a_directional_relationship(): void
    {
        [$user, $vault, $relationshipTypeCategory] = $this->createOwnerAndCategory();

        $response = $this->actingAs($user)
            ->from('/vaults/'.$vault->id.'/adminland')
            ->post('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$relationshipTypeCategory->id.'/relationship-types', [
                'name' => 'Parent / child',
                'is_directed' => '1',
            ]);

        $response->assertRedirect('/vaults/'.$vault->id.'/adminland');
        $response->assertSessionHasErrors(['forward_name', 'reverse_name']);
        $this->assertSame(0, $relationshipTypeCategory->relationshipTypes()->count());
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
