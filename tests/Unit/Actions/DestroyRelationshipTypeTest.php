<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyRelationshipType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyRelationshipTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_relationship_type_and_logs_the_action(): void
    {
        Queue::fake();
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType(['name' => 'Parent']);

        new DestroyRelationshipType($user, $relationshipType)->execute();

        $this->assertModelMissing($relationshipType);
        Queue::assertPushedOn(
            'low',
            LogUserAction::class,
            fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::RelationshipTypeDeletion
                && $job->parameters === ['name' => 'Parent']
            ),
        );
    }

    #[Test]
    public function it_logs_the_translated_name(): void
    {
        Queue::fake();
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType([
            'name' => null,
            'name_translation_key' => 'Parent',
        ]);

        new DestroyRelationshipType($user, $relationshipType)->execute();

        Queue::assertPushed(
            LogUserAction::class,
            fn (LogUserAction $job): bool => $job->parameters === ['name' => 'Parent'],
        );
    }

    #[Test]
    public function it_rejects_a_non_owner(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType();
        $user->memberOf($relationshipType->vault)->update(['role' => PermissionEnum::Viewer->value]);

        new DestroyRelationshipType($user, $relationshipType)->execute();
    }

    #[Test]
    public function it_rejects_a_user_outside_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [, $relationshipType] = $this->createOwnerAndRelationshipType();

        new DestroyRelationshipType($this->createUser(), $relationshipType)->execute();
    }

    #[Test]
    public function it_rejects_a_relationship_type_that_cannot_be_deleted(): void
    {
        $this->expectException(ModelNotFoundException::class);
        [$user, $relationshipType] = $this->createOwnerAndRelationshipType(['can_be_deleted' => false]);

        new DestroyRelationshipType($user, $relationshipType)->execute();
    }

    /** @param array<string, mixed> $attributes */
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
