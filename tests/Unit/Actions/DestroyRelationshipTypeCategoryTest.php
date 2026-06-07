<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyRelationshipTypeCategory;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipTypeCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyRelationshipTypeCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_relationship_type_category(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Family',
        ]);

        new DestroyRelationshipTypeCategory(user: $user, relationshipTypeCategory: $category)->execute();

        $this->assertModelMissing($category);
        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::RelationshipTypeCategoryDeletion
                && $job->vault->id === $vault->id
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Family']
            ),
        );
    }

    #[Test]
    public function it_logs_the_translated_name_when_the_name_is_null(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => null,
            'name_translation_key' => 'Family',
        ]);

        new DestroyRelationshipTypeCategory(user: $user, relationshipTypeCategory: $category)->execute();

        Queue::assertPushed(
            LogUserAction::class,
            fn (LogUserAction $job): bool => $job->parameters === ['name' => 'Family'],
        );
    }

    #[Test]
    public function it_fails_if_user_is_not_part_of_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $category = RelationshipTypeCategory::factory()->create();

        new DestroyRelationshipTypeCategory(user: $this->createUser(), relationshipTypeCategory: $category)->execute();
    }

    #[Test]
    public function it_fails_if_user_is_only_viewer(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Viewer->value);
        $category = RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id]);

        new DestroyRelationshipTypeCategory(user: $user, relationshipTypeCategory: $category)->execute();
    }

    #[Test]
    public function it_fails_if_the_category_cannot_be_deleted(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        $category = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'can_be_deleted' => false,
        ]);

        new DestroyRelationshipTypeCategory(user: $user, relationshipTypeCategory: $category)->execute();
    }
}
