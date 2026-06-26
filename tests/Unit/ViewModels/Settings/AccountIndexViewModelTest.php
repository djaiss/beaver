<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Settings;

use App\Enums\PermissionEnum;
use App\ViewModels\Settings\AccountIndexViewModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_vaults_that_will_be_deleted(): void
    {
        $user = $this->createUser();
        $vault = $this->createVault('Central Perk');

        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);

        $vaults = new AccountIndexViewModel($user)->vaultsToDelete();

        $this->assertCount(1, $vaults);
        $this->assertSame('Central Perk', $vaults->first()->name);
        $this->assertSame(route('vault.show', $vault->id), $vaults->first()->link);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $vaults->first()->avatar);
    }

    #[Test]
    public function it_returns_the_vaults_that_will_not_be_deleted(): void
    {
        $user = $this->createUser();
        $otherOwner = $this->createUser();
        $vault = $this->createVault('Dunder Mifflin');

        $this->assignUserToVault($user, $vault, PermissionEnum::Owner->value);
        $this->assignUserToVault($otherOwner, $vault, PermissionEnum::Owner->value);

        $vaults = new AccountIndexViewModel($user)->vaultsNotDeleted();

        $this->assertCount(1, $vaults);
        $this->assertSame('Dunder Mifflin', $vaults->first()->name);
        $this->assertSame(route('vault.show', $vault->id), $vaults->first()->link);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $vaults->first()->avatar);
    }

    #[Test]
    public function it_tests_the_urls(): void
    {
        $viewModel = new AccountIndexViewModel($this->createUser());
        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/vaults', $url->dashboard);
        $this->assertSame(config('app.url').'/settings', $url->settings);
        $this->assertSame(config('app.url').'/settings/account', $url->deleteAccount);
    }
}
