<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Vault\Adminland;

use App\Models\Gender;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\ViewModels\Vault\Adminland\AdminlandIndexViewModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminlandIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_vault(): void
    {
        $vault = $this->createVault('Central Perk');

        $this->assertTrue(new AdminlandIndexViewModel($vault)->vault()->is($vault));
    }

    #[Test]
    public function it_returns_the_genders_in_position_order(): void
    {
        $vault = $this->createVault();
        Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Second',
            'position' => 2,
        ]);
        $firstGender = Gender::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'First',
            'position' => 1,
        ]);

        $genders = new AdminlandIndexViewModel($vault)->genders();

        $this->assertCount(2, $genders);
        $this->assertSame([$firstGender->id, 1], [$genders->first()->id, $genders->first()->position]);
        $this->assertSame('First', $genders->first()->name);
        $this->assertSame(route('vault.adminland.genders.edit', ['vaultId' => $vault->id, 'gender' => $firstGender->id]), $genders->first()->editUrl);
        $this->assertSame(route('vault.adminland.genders.destroy', ['vaultId' => $vault->id, 'gender' => $firstGender->id]), $genders->first()->destroyUrl);
        $this->assertSame(route('vault.adminland.genders.position.update', ['vaultId' => $vault->id, 'gender' => $firstGender->id]), $genders->first()->positionUrl);
    }

    #[Test]
    public function it_returns_relationship_type_categories_and_types_in_position_order(): void
    {
        $vault = $this->createVault();
        RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Work',
            'position' => 2,
        ]);
        $firstCategory = RelationshipTypeCategory::factory()->create([
            'vault_id' => $vault->id,
            'name' => 'Family',
            'position' => 1,
            'can_be_deleted' => false,
        ]);
        RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $firstCategory->id,
            'name' => 'Sibling',
            'position' => 2,
        ]);
        $firstRelationshipType = RelationshipType::factory()->create([
            'vault_id' => $vault->id,
            'relationship_type_category_id' => $firstCategory->id,
            'name' => 'Parent',
            'position' => 1,
            'can_be_deleted' => false,
        ]);

        $categories = new AdminlandIndexViewModel($vault)->relationshipTypeCategories();

        $this->assertCount(2, $categories);
        $this->assertSame('Family', $categories->first()->name);
        $this->assertFalse($categories->first()->can_be_deleted);
        $this->assertSame(route('vault.adminland.relationship_types.new', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $firstCategory->id]), $categories->first()->newRelationshipTypeUrl);
        $this->assertSame(route('vault.adminland.relationship_type_categories.edit', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $firstCategory->id]), $categories->first()->editUrl);
        $this->assertSame(route('vault.adminland.relationship_type_categories.destroy', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $firstCategory->id]), $categories->first()->destroyUrl);
        $this->assertSame('Parent', $categories->first()->relationshipTypes->first()->name);
        $this->assertSame(1, $categories->first()->relationshipTypes->first()->position);
        $this->assertFalse($categories->first()->relationshipTypes->first()->can_be_deleted);
        $this->assertSame(route('vault.adminland.relationship_types.edit', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $firstCategory->id, 'relationshipType' => $firstRelationshipType->id]), $categories->first()->relationshipTypes->first()->editUrl);
        $this->assertSame(route('vault.adminland.relationship_types.destroy', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $firstCategory->id, 'relationshipType' => $firstRelationshipType->id]), $categories->first()->relationshipTypes->first()->destroyUrl);
    }

    #[Test]
    public function it_tests_the_urls(): void
    {
        $vault = $this->createVault();
        $url = new AdminlandIndexViewModel($vault)->url();

        $this->assertSame(route('vault.show', $vault), $url->vaultShow);
        $this->assertSame(route('vault.adminland.update', ['vaultId' => $vault->id]), $url->update);
        $this->assertSame(route('vault.adminland.index', ['vaultId' => $vault->id]), $url->index);
        $this->assertSame(route('settings.security.index'), $url->security);
        $this->assertSame(route('vault.adminland.manage.index', ['vaultId' => $vault->id]), $url->manage);
        $this->assertSame(route('vault.adminland.genders.new', ['vaultId' => $vault->id]), $url->newGender);
        $this->assertSame(route('vault.adminland.relationship_type_categories.new', ['vaultId' => $vault->id]), $url->newRelationshipTypeCategory);
    }
}
