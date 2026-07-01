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
}
