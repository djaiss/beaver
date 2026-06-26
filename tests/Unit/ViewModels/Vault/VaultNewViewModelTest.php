<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Vault;

use App\ViewModels\Vault\VaultNewViewModel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VaultNewViewModelTest extends TestCase
{
    #[Test]
    public function it_tests_the_urls(): void
    {
        $viewModel = new VaultNewViewModel;
        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/vaults', $url->vaultIndex);
        $this->assertSame(config('app.url').'/vaults', $url->vaultCreate);
    }
}
