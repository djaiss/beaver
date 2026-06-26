<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Settings;

use App\ViewModels\Settings\ApiKeyCreateViewModel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ApiKeyCreateViewModelTest extends TestCase
{
    #[Test]
    public function it_tests_the_urls(): void
    {
        $viewModel = new ApiKeyCreateViewModel;
        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/settings/api-keys', $url->store);
        $this->assertSame(config('app.url').'/settings/security', $url->settings);
    }
}
