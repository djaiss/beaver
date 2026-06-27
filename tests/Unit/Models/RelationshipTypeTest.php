<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RelationshipTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault_when_defined(): void
    {
        $relationshipType = RelationshipType::factory()->create();

        $this->assertTrue($relationshipType->vault()->exists());
    }

    #[Test]
    public function it_requires_a_vault(): void
    {
        $this->expectException(QueryException::class);

        RelationshipType::factory()->create([
            'vault_id' => null,
            'relationship_type_category_id' => RelationshipTypeCategory::factory(),
        ]);
    }

    #[Test]
    public function it_belongs_to_a_relationship_type_category(): void
    {
        $relationshipType = RelationshipType::factory()->create();

        $this->assertTrue($relationshipType->relationshipTypeCategory()->exists());
    }

    #[Test]
    public function it_returns_the_name_when_defined(): void
    {
        $relationshipType = RelationshipType::factory()->make([
            'name' => 'Parent',
            'name_translation_key' => null,
        ]);

        $this->assertSame('Parent', $relationshipType->name);
    }

    #[Test]
    public function it_returns_the_translated_name_when_name_is_null(): void
    {
        $relationshipType = RelationshipType::factory()->make([
            'name' => null,
            'name_translation_key' => 'Parent',
        ]);

        $this->assertSame('Parent', $relationshipType->name);
    }

    #[Test]
    public function it_returns_the_forward_name_when_defined(): void
    {
        $relationshipType = RelationshipType::factory()->make([
            'forward_name' => 'Parent of',
            'forward_name_translation_key' => null,
        ]);

        $this->assertSame('Parent of', $relationshipType->forward_name);
    }

    #[Test]
    public function it_returns_the_translated_forward_name_when_forward_name_is_null(): void
    {
        $relationshipType = RelationshipType::factory()->make([
            'forward_name' => null,
            'forward_name_translation_key' => 'Parent of',
        ]);

        $this->assertSame('Parent of', $relationshipType->forward_name);
    }

    #[Test]
    public function it_returns_the_reverse_name_when_defined(): void
    {
        $relationshipType = RelationshipType::factory()->make([
            'reverse_name' => 'Child of',
            'reverse_name_translation_key' => null,
        ]);

        $this->assertSame('Child of', $relationshipType->reverse_name);
    }

    #[Test]
    public function it_returns_the_translated_reverse_name_when_reverse_name_is_null(): void
    {
        $relationshipType = RelationshipType::factory()->make([
            'reverse_name' => null,
            'reverse_name_translation_key' => 'Child of',
        ]);

        $this->assertSame('Child of', $relationshipType->reverse_name);
    }
}
