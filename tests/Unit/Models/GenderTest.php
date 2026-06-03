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
}
