<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\RelationshipTypeCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RelationshipTypeCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault(): void
    {
        $category = RelationshipTypeCategory::factory()->create();

        $this->assertTrue($category->vault()->exists());
    }

    #[Test]
    public function it_returns_the_name_when_defined(): void
    {
        $category = RelationshipTypeCategory::factory()->make([
            'name' => 'Family',
            'name_translation_key' => null,
        ]);

        $this->assertSame('Family', $category->name);
    }

    #[Test]
    public function it_returns_the_translated_name_when_name_is_null(): void
    {
        $category = RelationshipTypeCategory::factory()->make([
            'name' => null,
            'name_translation_key' => 'Family',
        ]);

        $this->assertSame('Family', $category->name);
    }
}
