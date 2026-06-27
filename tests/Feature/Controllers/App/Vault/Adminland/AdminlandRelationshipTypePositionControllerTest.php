<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandRelationshipTypePositionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_reorders_a_relationship_type_within_its_category(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $otherCategory = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $first = $this->createRelationshipType($vault->id, $category->id, 1);
        $second = $this->createRelationshipType($vault->id, $category->id, 2);
        $third = $this->createRelationshipType($vault->id, $category->id, 3);
        $unrelated = $this->createRelationshipType($vault->id, $otherCategory->id, 1);

        $response = $this->actingAs($user)
            ->put('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$category->id.'/relationship-types/'.$first->id.'/position', [
                'position' => 3,
            ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame(3, $first->refresh()->position);
        $this->assertSame(1, $second->refresh()->position);
        $this->assertSame(2, $third->refresh()->position);
        $this->assertSame(1, $unrelated->refresh()->position);
    }

    #[Test]
    public function it_validates_the_relationship_type_position(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $relationshipType = $this->createRelationshipType($vault->id, $category->id, 1);

        $response = $this->actingAs($user)
            ->from('/vaults/'.$vault->id.'/adminland')
            ->put('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$category->id.'/relationship-types/'.$relationshipType->id.'/position', [
                'position' => 0,
            ]);

        $response->assertRedirect('/vaults/'.$vault->id.'/adminland');
        $response->assertSessionHasErrors('position');
        $this->assertSame(1, $relationshipType->refresh()->position);
    }

    #[Test]
    public function it_rejects_reordering_a_relationship_type_from_another_category(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $otherCategory = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $relationshipType = $this->createRelationshipType($vault->id, $otherCategory->id, 1);

        $this->actingAs($user)
            ->put('/vaults/'.$vault->id.'/adminland/relationship-type-categories/'.$category->id.'/relationship-types/'.$relationshipType->id.'/position', [
                'position' => 1,
            ])
            ->assertNotFound();
    }

    private function createRelationshipType(int $vaultId, int $categoryId, int $position): RelationshipType
    {
        return RelationshipType::factory()->create([
            'vault_id' => $vaultId,
            'relationship_type_category_id' => $categoryId,
            'position' => $position,
        ]);
    }
}
