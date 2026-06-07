<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Gender;
use App\Models\Person;
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

        $this->assertSame('Male', $gender->name);
    }

    #[Test]
    public function it_has_many_persons(): void
    {
        $gender = Gender::factory()->create();
        Person::factory()->create([
            'gender_id' => $gender->id,
        ]);

        $this->assertTrue($gender->persons()->exists());
    }

    #[Test]
    public function it_returns_translated_name_when_name_is_null(): void
    {
        $gender = Gender::factory()->make([
            'name' => null,
            'name_translation_key' => 'Male',
        ]);

        $this->assertSame('Male', $gender->name);
    }
}
