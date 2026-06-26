<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Vault;

use App\ViewModels\Vault\VaultIndexItemData;
use App\ViewModels\Vault\VaultIndexViewModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VaultIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_users_vaults(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault('Central Perk');

        $this->assignUserToVault($user, $vault);

        $vaults = new VaultIndexViewModel($user)->vaults();

        $this->assertCount(1, $vaults);
        $this->assertInstanceOf(VaultIndexItemData::class, $vaults->first());
        $this->assertSame('Central Perk', $vaults->first()->name);
        $this->assertSame(route('vault.show', $vault), $vaults->first()->url);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $vaults->first()->avatar);
    }

    #[Test]
    public function it_tests_the_urls(): void
    {
        $user = $this->createUser();
        $viewModel = new VaultIndexViewModel($user);
        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/vaults/create', $url->create);
        $this->assertSame(config('app.url').'/vaults/join', $url->join);
    }
}
