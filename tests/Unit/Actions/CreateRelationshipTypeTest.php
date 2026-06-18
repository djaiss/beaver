<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateRelationshipType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateRelationshipTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_relationship_type(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        $relationshipType = new CreateRelationshipType($user, $vault, $category, 'parent', 'Parent', true)->execute();

        $this->assertInstanceOf(RelationshipType::class, $relationshipType);
        $this->assertSame('Parent', $relationshipType->name);
        $this->assertTrue($relationshipType->is_directed);
        $this->assertSame(1, $relationshipType->position);
        Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::RelationshipTypeCreation
            && $job->parameters === ['name' => 'Parent']
        ));
    }

    #[Test]
    public function it_sanitizes_the_key_and_name_and_increments_position(): void
    {
        [$user, $vault, $category] = $this->createOwnerAndCategory();
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            'position' => 5,
        ]);

        $relationshipType = new CreateRelationshipType(
            $user,
            $vault,
            $category,
            ' <strong>parent</strong> ',
            ' <strong>Parent</strong> ',
        )->execute();

        $this->assertSame('parent', $relationshipType->key);
        $this->assertSame('Parent', $relationshipType->name);
        $this->assertSame(6, $relationshipType->position);
    }

    #[Test]
    public function it_generates_a_key_when_none_is_provided(): void
    {
        [$user, $vault, $category] = $this->createOwnerAndCategory();

        $relationshipType = new CreateRelationshipType($user, $vault, $category, null, 'Parent')->execute();

        $this->assertStringStartsWith('custom-', $relationshipType->key);
    }

    #[Test]
    public function it_rejects_a_duplicate_key_in_the_category(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [$user, $vault, $category] = $this->createOwnerAndCategory();
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            'key' => 'parent',
        ]);

        new CreateRelationshipType($user, $vault, $category, 'parent', 'Parent')->execute();
    }

    #[Test]
    public function it_rejects_a_category_from_another_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [$user, $vault] = $this->createOwnerAndCategory();
        $category = RelationshipTypeCategory::factory()->create();

        new CreateRelationshipType($user, $vault, $category, 'parent', 'Parent')->execute();
    }

    #[Test]
    public function it_rejects_a_non_owner(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Viewer->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        new CreateRelationshipType($user, $vault, $category, 'parent', 'Parent')->execute();
    }

    #[Test]
    public function it_rejects_a_user_outside_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $vault = $this->createVault();
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        new CreateRelationshipType($this->createUser(), $vault, $category, 'parent', 'Parent')->execute();
    }

    /** @return array{0: User, 1: Vault, 2: RelationshipTypeCategory} */
    private function createOwnerAndCategory(): array
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        return [$user, $vault, $category];
    }
}
