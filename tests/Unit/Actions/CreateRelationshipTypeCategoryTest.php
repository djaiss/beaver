<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateRelationshipTypeCategory;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipTypeCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateRelationshipTypeCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_relationship_type_category(): void
    {
        Queue::fake();
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);

        $category = new CreateRelationshipTypeCategory(
            user: $user,
            vault: $vault,
            key: 'family',
            name: 'Family',
        )->execute();

        $this->assertInstanceOf(RelationshipTypeCategory::class, $category);
        $this->assertDatabaseHas('relationship_type_categories', [
            'id' => $category->id,
            'vault_id' => $vault->id,
            'key' => 'family',
            'position' => 1,
            'can_be_deleted' => true,
        ]);
        $this->assertSame('Family', $category->name);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::RelationshipTypeCategoryCreation
                && $job->vault->id === $vault->id
                && $job->user->id === $user->id
                && $job->parameters === ['name' => 'Family']
            ),
        );
    }

    #[Test]
    public function it_sanitizes_the_key_and_name(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);

        $category = new CreateRelationshipTypeCategory(
            user: $user,
            vault: $vault,
            key: ' <strong>family</strong> ',
            name: ' <strong>Family</strong> ',
        )->execute();

        $this->assertSame('family', $category->key);
        $this->assertSame('Family', $category->name);
    }

    #[Test]
    public function it_increments_position_automatically(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id, 'position' => 5]);

        $category = new CreateRelationshipTypeCategory($user, $vault, 'family', 'Family')->execute();

        $this->assertSame(6, $category->position);
    }

    #[Test]
    public function it_fails_if_the_key_already_exists_in_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        RelationshipTypeCategory::factory()->create(['vault_id' => $vault->id, 'key' => 'family']);

        new CreateRelationshipTypeCategory($user, $vault, 'family', 'Family')->execute();
    }

    #[Test]
    public function it_allows_the_same_key_in_another_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $otherVault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Owner->value);
        RelationshipTypeCategory::factory()->create(['vault_id' => $otherVault->id, 'key' => 'family']);

        $category = new CreateRelationshipTypeCategory($user, $vault, 'family', 'Family')->execute();

        $this->assertModelExists($category);
    }

    #[Test]
    public function it_fails_if_user_is_not_part_of_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        new CreateRelationshipTypeCategory(
            user: $this->createUser(),
            vault: $this->createVault(),
            key: 'family',
            name: 'Family',
        )->execute();
    }

    #[Test]
    public function it_fails_if_user_is_only_viewer(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(user: $user, vault: $vault, role: PermissionEnum::Viewer->value);

        new CreateRelationshipTypeCategory($user, $vault, 'family', 'Family')->execute();
    }
}
