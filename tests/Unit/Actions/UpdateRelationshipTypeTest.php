<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateRelationshipType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateRelationshipTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_relationship_type_and_logs_the_action(): void
    {
        Queue::fake();
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType();

        $updated = new UpdateRelationshipType(
            user: $user,
            relationshipType: $relationshipType,
            name: 'Parent / child',
            isDirected: true,
            forwardName: 'Parent',
            reverseName: 'Child',
        )->execute();

        $this->assertSame('Parent / child', $updated->name);
        $this->assertSame('Parent', $updated->forward_name);
        $this->assertSame('Child', $updated->reverse_name);
        $this->assertTrue($updated->is_directed);
        Queue::assertPushedOn(
            'low',
            LogUserAction::class,
            fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::RelationshipTypeUpdate
                && $job->parameters === ['name' => 'Parent / child']
            ),
        );
    }

    #[Test]
    public function it_sanitizes_a_nullable_name(): void
    {
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType([
            'name_translation_key' => 'Parent',
        ]);

        $updated = new UpdateRelationshipType($user, $relationshipType, null, false)->execute();

        $this->assertSame('Parent', $updated->name);
        $this->assertFalse($updated->is_directed);
    }

    #[Test]
    public function it_sanitizes_direction_names(): void
    {
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType();

        $updated = new UpdateRelationshipType(
            user: $user,
            relationshipType: $relationshipType,
            name: 'Parent / child',
            isDirected: true,
            forwardName: ' <strong>Parent</strong> ',
            reverseName: ' <strong>Child</strong> ',
        )->execute();

        $this->assertSame('Parent', $updated->forward_name);
        $this->assertSame('Child', $updated->reverse_name);
    }

    #[Test]
    public function it_clears_direction_names_when_relationship_becomes_non_directional(): void
    {
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType([
            'forward_name' => 'Parent',
            'reverse_name' => 'Child',
            'is_directed' => true,
        ]);

        $updated = new UpdateRelationshipType($user, $relationshipType, 'Relative', false)->execute();

        $this->assertNull($updated->getRawOriginal('forward_name'));
        $this->assertNull($updated->getRawOriginal('reverse_name'));
        $this->assertFalse($updated->is_directed);
    }

    #[Test]
    public function it_reorders_positions_within_the_category(): void
    {
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType(['position' => 1]);
        $second = RelationshipType::factory()->create([
            'vault_id' => $relationshipType->vault_id,
            'relationship_type_category_id' => $relationshipType->relationship_type_category_id,
            'position' => 2,
        ]);
        $third = RelationshipType::factory()->create([
            'vault_id' => $relationshipType->vault_id,
            'relationship_type_category_id' => $relationshipType->relationship_type_category_id,
            'position' => 3,
        ]);

        new UpdateRelationshipType($user, $relationshipType, 'Parent', false, 3)->execute();

        $this->assertSame(3, $relationshipType->refresh()->position);
        $this->assertSame(1, $second->refresh()->position);
        $this->assertSame(2, $third->refresh()->position);
    }

    #[Test]
    public function it_rejects_an_invalid_position(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType();

        new UpdateRelationshipType($user, $relationshipType, 'Parent', false, 0)->execute();
    }

    #[Test]
    public function it_rejects_a_non_owner(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType();
        $user->memberOf($relationshipType->vault)->update(['role' => PermissionEnum::Viewer->value]);

        new UpdateRelationshipType($user, $relationshipType, 'Parent', false)->execute();
    }

    #[Test]
    public function it_rejects_a_user_outside_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [, $relationshipType] = $this->createOwnerAndRelationshipType();

        new UpdateRelationshipType($this->createUser(), $relationshipType, 'Parent', false)->execute();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{0: User, 1: RelationshipType}
     */
    private function createOwnerAndRelationshipType(array $attributes = []): array
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);
        $relationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $category->id,
            ...$attributes,
        ]);

        return [$user, $relationshipType];
    }
}
