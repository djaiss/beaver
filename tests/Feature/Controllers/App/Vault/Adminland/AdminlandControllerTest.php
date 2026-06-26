<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Vault\Adminland;

use App\Enums\PermissionEnum;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_adminland(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->get("/vaults/{$vault->id}/adminland");

        $response->assertOk();
    }

    #[Test]
    public function it_shows_relationship_type_categories_and_types_in_position_order(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $secondCategory = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Work',
            'position' => 2,
        ]);
        $firstCategory = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Family',
            'position' => 1,
        ]);
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $firstCategory->id,
            'name' => 'Sibling',
            'position' => 2,
        ]);
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $firstCategory->id,
            'name' => 'Parent',
            'position' => 1,
        ]);

        $response = $this->actingAs($user)->get("/vaults/{$vault->id}/adminland");

        $response->assertOk();
        $response->assertSeeInOrder(['Family', 'Parent', 'Sibling', 'Work']);
        $response->assertSee(
            'group/category flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-100 p-3 transition-colors duration-200 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800',
            false,
        );
        $response->assertSee('invisible text-sm group-hover/category:visible', false);
        $response->assertSee('divide-y divide-gray-200 dark:divide-gray-700', false);
        $response->assertViewHas(
            'relationshipTypeCategories',
            fn ($categories): bool => (
                $categories->pluck('id')->all() === [$firstCategory->id, $secondCategory->id]
                && $categories->first()->relationshipTypes->pluck('position')->all() === [1, 2]
            ),
        );
    }

    #[Test]
    public function it_restricts_adminland(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Viewer->value,
        );

        $response = $this->actingAs($user)->get("/vaults/{$vault->id}/adminland");

        $response->assertStatus(403);
    }

    #[Test]
    public function it_updates_the_vault(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->put("/vaults/{$vault->id}/adminland", [
            'vault_name' => 'Central Perk',
        ]);

        $response->assertRedirect(route('vault.adminland.index', $vault->id));
        $this->assertSame('Central Perk', $vault->refresh()->name);
    }

    #[Test]
    public function it_validates_the_vault_name_when_updating_the_vault(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault();
        $this->assignUserToVault(
            user: $user,
            vault: $vault,
            role: PermissionEnum::Owner->value,
        );

        $response = $this->actingAs($user)->from("/vaults/{$vault->id}/adminland")->put(
            "/vaults/{$vault->id}/adminland",
            [
                'vault_name' => 'Central@ / Perk!',
            ],
        );

        $response->assertRedirect("/vaults/{$vault->id}/adminland");
        $response->assertSessionHasErrors('vault_name');
        $this->assertSame('New York Public Library', $vault->refresh()->name);
    }
}
