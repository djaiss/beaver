<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\PopulateVault;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateVaultTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_populates_a_vault(): void
    {
        $vault = $this->createVault();

        new PopulateVault($vault)->handle();

        $this->assertEquals(3, $vault->genders()->count());
        $this->assertEquals(12, $vault->relationshipTypeCategories()->count());
    }
}
