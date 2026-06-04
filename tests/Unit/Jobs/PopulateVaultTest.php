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
    public function it_populates_a_vault_with_default_genders(): void
    {
        $vault = $this->createVault();

        new PopulateVault($vault)->handle();

        $this->assertEquals(
            [
                'app/shared.genders.man',
                'app/shared.genders.woman',
                'app/shared.genders.other',
            ],
            $vault->genders()
                ->orderBy('position')
                ->pluck('name_translation_key')
                ->all(),
        );
    }
}
