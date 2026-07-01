<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\SpecialDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SpecialDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_vault(): void
    {
        $specialDate = SpecialDate::factory()->create();

        $this->assertTrue($specialDate->vault()->exists());
    }

    #[Test]
    public function it_belongs_to_a_person(): void
    {
        $specialDate = SpecialDate::factory()->create();

        $this->assertTrue($specialDate->person()->exists());
    }

    #[Test]
    public function it_casts_is_approximate_to_a_boolean(): void
    {
        $specialDate = SpecialDate::factory()->make(['is_approximate' => true]);

        $this->assertTrue($specialDate->is_approximate);
    }

    #[Test]
    public function it_defaults_is_approximate_to_false(): void
    {
        $specialDate = new SpecialDate;

        $this->assertFalse($specialDate->is_approximate);
    }
}
