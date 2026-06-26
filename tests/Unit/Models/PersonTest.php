<?php

declare(strict_types = 1);

namespace Tests\Unit\Models;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault(): void
    {
        $person = Person::factory()->create();

        $this->assertTrue($person->vault()->exists());
    }

    #[Test]
    public function it_belongs_to_a_gender(): void
    {
        $person = Person::factory()->create();

        $this->assertTrue($person->gender()->exists());
    }

    #[Test]
    public function it_gets_the_name(): void
    {
        $person = Person::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
        ]);

        $this->assertEquals(
            'Ross Geller',
            $person->name,
        );
    }
}
