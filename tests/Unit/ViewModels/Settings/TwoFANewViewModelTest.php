<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Settings;

use App\ViewModels\Settings\TwoFANewViewModel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TwoFANewViewModelTest extends TestCase
{
    #[Test]
    public function it_returns_the_secret(): void
    {
        $viewModel = new TwoFANewViewModel([
            'secret' => 'fixed-secret',
            'qrCodeSvg' => '<svg>fixed</svg>',
        ]);

        $secret = $viewModel->secret();

        $this->assertSame('fixed-secret', $secret->secret);
        $this->assertSame('<svg>fixed</svg>', $secret->qrCodeSvg);
    }

    #[Test]
    public function it_tests_the_urls(): void
    {
        $viewModel = new TwoFANewViewModel([
            'secret' => 'fixed-secret',
            'qrCodeSvg' => '<svg>fixed</svg>',
        ]);

        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/settings/security/2fa', $url->store);
        $this->assertSame(config('app.url').'/settings/security', $url->settings);
    }
}
