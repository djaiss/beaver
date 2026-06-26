<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Settings;

use App\ViewModels\Settings\SecurityIndexViewModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_users_tokens(): void
    {
        Date::setTestNow('2026-01-01 00:00:00');

        $user = $this->createUser();
        $accessToken = $user->createToken('Test API Key')->accessToken;
        $accessToken->forceFill([
            'last_used_at' => Date::now()->subDay(),
        ])->save();

        $tokens = new SecurityIndexViewModel($user->fresh())->tokens();

        $this->assertCount(1, $tokens);
        $this->assertSame($accessToken->id, $tokens->first()->id);
        $this->assertSame('Test API Key', $tokens->first()->name);
        $this->assertSame('1 day ago', $tokens->first()->last_used);
        $this->assertFalse($tokens->first()->just_added);
        $this->assertSame($accessToken->token, $tokens->first()->token);
        $this->assertSame(route('settings.api-keys.destroy', $accessToken->id), $tokens->first()->url);
    }

    #[Test]
    public function it_knows_when_the_user_has_2fa(): void
    {
        $user = $this->createUser([
            'two_factor_confirmed_at' => now(),
        ]);

        $this->assertTrue(new SecurityIndexViewModel($user)->has2fa());
    }

    #[Test]
    public function it_knows_when_the_user_does_not_have_2fa(): void
    {
        $user = $this->createUser([
            'two_factor_confirmed_at' => null,
        ]);

        $this->assertFalse(new SecurityIndexViewModel($user)->has2fa());
    }

    #[Test]
    public function it_tests_the_urls(): void
    {
        $viewModel = new SecurityIndexViewModel($this->createUser());
        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/vaults', $url->vault);
        $this->assertSame(config('app.url').'/settings/security/password', $url->updatePassword);
        $this->assertSame(config('app.url').'/settings/security/2fa/new', $url->new2fa);
        $this->assertSame(config('app.url').'/settings/security/2fa', $url->destroy2fa);
        $this->assertSame(config('app.url').'/settings/security/recovery-codes', $url->showRecoveryCodes);
        $this->assertSame(config('app.url').'/settings/security/auto-delete-account', $url->updateAutoDelete);
        $this->assertSame(config('app.url').'/settings/api-keys/create', $url->createApiKey);
    }
}
