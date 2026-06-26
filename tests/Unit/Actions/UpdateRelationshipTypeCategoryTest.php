<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateRelationshipTypeCategory;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateRelationshipTypeCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_relationship_type_category(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Family',
            'position' => 2,
        ]);

        $updatedCategory = new UpdateRelationshipTypeCategory(
            user: $user,
            relationshipTypeCategory: $category,
            name: 'Close family',
        )->execute();

        $this->assertInstanceOf(RelationshipTypeCategory::class, $updatedCategory);
        $this->assertSame('Close family', $updatedCategory->name);
        $this->assertSame(2, $updatedCategory->position);
        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::RelationshipTypeCategoryUpdate
                && $job->vault->id === $vault->id
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Close family']
            ),
        );
    }

    #[Test]
    public function it_updates_position_when_provided(): void
    {
        [$user, $category] = $this->createOwnerAndCategory(['position' => 1]);
        RelationshipTypeCategory::factory()->create(['vault_id' => $category->vault_id, 'position' => 2]);
        RelationshipTypeCategory::factory()->create(['vault_id' => $category->vault_id, 'position' => 3]);

        $updatedCategory = new UpdateRelationshipTypeCategory($user, $category, 'Family', 3)->execute();

        $this->assertSame(3, $updatedCategory->position);
    }

    #[Test]
    public function it_reorders_positions_when_moving_down(): void
    {
        [$user, $category2] = $this->createOwnerAndCategory(['position' => 2]);
        $vaultId = $category2->vault_id;
        $category1 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 1]);
        $category3 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 3]);
        $category4 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 4]);
        $category5 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 5]);

        new UpdateRelationshipTypeCategory($user, $category2, 'Family', 4)->execute();

        $this->assertSame(1, $category1->refresh()->position);
        $this->assertSame(4, $category2->refresh()->position);
        $this->assertSame(2, $category3->refresh()->position);
        $this->assertSame(3, $category4->refresh()->position);
        $this->assertSame(5, $category5->refresh()->position);
    }

    #[Test]
    public function it_reorders_positions_when_moving_up(): void
    {
        [$user, $category4] = $this->createOwnerAndCategory(['position' => 4]);
        $vaultId = $category4->vault_id;
        $category1 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 1]);
        $category2 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 2]);
        $category3 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 3]);
        $category5 = RelationshipTypeCategory::factory()->create(['vault_id' => $vaultId, 'position' => 5]);

        new UpdateRelationshipTypeCategory($user, $category4, 'Family', 2)->execute();

        $this->assertSame(1, $category1->refresh()->position);
        $this->assertSame(3, $category2->refresh()->position);
        $this->assertSame(4, $category3->refresh()->position);
        $this->assertSame(2, $category4->refresh()->position);
        $this->assertSame(5, $category5->refresh()->position);
    }

    #[Test]
    public function it_fails_if_user_is_not_part_of_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $category = RelationshipTypeCategory::factory()->create();

        new UpdateRelationshipTypeCategory($this->createUser(), $category, 'Family')->execute();
    }

    #[Test]
    public function it_fails_if_user_is_only_viewer(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Viewer->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        new UpdateRelationshipTypeCategory($user, $category, 'Family')->execute();
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{0: User, 1: RelationshipTypeCategory}
     */
    private function createOwnerAndCategory(array $attributes = []): array
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            ...$attributes,
        ]);

        return [$user, $category];
    }
}
