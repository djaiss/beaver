<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Gender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault(): void
    {
        $gender = Gender::factory()->create();

        $this->assertTrue($gender->vault()->exists());
    }

    #[Test]
    public function it_returns_name_when_set(): void
    {
        $gender = Gender::factory()->make([
            'name' => 'Male',
            'name_translation_key' => null,
        ]);

        $this->assertEquals('Male', $gender->getName());
    }

    #[Test]
    public function it_returns_translated_name_when_name_is_null(): void
    {
        $gender = Gender::factory()->make([
            'name' => null,
            'name_translation_key' => 'Male',
        ]);

        $this->assertEquals('Male', $gender->getName());
    }
}
