<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\MaritalStatus;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MaritalStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault(): void
    {
        $maritalStatus = MaritalStatus::factory()->create();

        $this->assertTrue($maritalStatus->vault()->exists());
    }

    #[Test]
    public function it_has_many_persons(): void
    {
        $maritalStatus = MaritalStatus::factory()->create();
        Person::factory()->create([
            'marital_status_id' => $maritalStatus->id,
        ]);

        $this->assertTrue($maritalStatus->persons()->exists());
    }

    #[Test]
    public function it_returns_name_when_set(): void
    {
        $maritalStatus = MaritalStatus::factory()->make([
            'name' => 'Married',
            'name_translation_key' => null,
        ]);

        $this->assertSame('Married', $maritalStatus->getName());
    }

    #[Test]
    public function it_returns_translated_name_when_name_is_null(): void
    {
        $maritalStatus = MaritalStatus::factory()->make([
            'name' => null,
            'name_translation_key' => 'Married',
        ]);

        $this->assertSame('Married', $maritalStatus->getName());
    }
}
