<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Settings;

use App\Models\EmailSent;
use App\Models\Log;
use App\ViewModels\Settings\SettingsIndexViewModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingsIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_latest_logs_for_the_user(): void
    {
        Date::setTestNow('2026-01-01 12:00:00');

        $user = $this->createUser([
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
        ]);
        $vault = $this->createVault('Central Perk');

        Log::factory()->create([
            'user_id' => $this->createUser()->id,
            'created_at' => Date::now()->addMinute(),
        ]);

        foreach (range(1, 6) as $index) {
            Log::factory()->create([
                'user_id' => $user->id,
                'vault_id' => $vault->id,
                'action' => 'log.test.action',
                'parameters' => ['name' => "Log {$index}"],
                'created_at' => Date::now()->addMinutes($index),
            ]);
        }

        $viewModel = new SettingsIndexViewModel($user);
        $logs = $viewModel->logs();

        $this->assertCount(5, $logs);
        $this->assertTrue($viewModel->hasMoreLogs());
        $this->assertSame('Chandler Bing', $logs->first()->username);
        $this->assertSame('Central Perk', $logs->first()->vault_name);
        $this->assertSame(route('vault.show', $vault->id), $logs->first()->vault_link);
        $this->assertSame('log.test.action', $logs->first()->action);
        $this->assertSame(__('app/settings/logs.user_action.log.test.action', ['name' => 'Log 6']), $logs->first()->description);
        $this->assertSame('2026-01-01 12:06:00', $logs->first()->created_at);
        $this->assertSame('2 minutes from now', $logs->last()->created_at_human);
    }

    #[Test]
    public function it_returns_the_latest_emails_for_the_user(): void
    {
        $user = $this->createUser();

        EmailSent::factory()->create([
            'user_id' => $this->createUser()->id,
            'sent_at' => '2026-01-01 00:00:00',
        ]);

        foreach (range(1, 7) as $index) {
            EmailSent::factory()->create([
                'user_id' => $user->id,
                'email_address' => "chandler{$index}@example.com",
                'subject' => "Subject {$index}",
                'body' => "Body {$index}",
                'sent_at' => "2026-01-0{$index} 00:00:00",
                'delivered_at' => "2026-01-0{$index} 01:00:00",
                'bounced_at' => null,
            ]);
        }

        $viewModel = new SettingsIndexViewModel($user);
        $emails = $viewModel->emails();

        $this->assertCount(6, $emails);
        $this->assertTrue($viewModel->hasMoreEmails());
        $this->assertSame('chandler7@example.com', $emails->first()->email_address);
        $this->assertSame('Subject 7', $emails->first()->subject);
        $this->assertSame('Body 7', $emails->first()->body);
        $this->assertSame('2026-01-07 00:00:00', $emails->first()->sent_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-01-07 01:00:00', $emails->first()->delivered_at->format('Y-m-d H:i:s'));
        $this->assertNull($emails->first()->bounced_at);
    }

    #[Test]
    public function it_returns_the_user_information(): void
    {
        $user = $this->createUser([
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'nickname' => 'Chan',
            'email' => 'chandler@example.com',
            'locale' => 'en_US',
        ]);

        $viewModelUser = new SettingsIndexViewModel($user)->user();

        $this->assertSame('Chandler', $viewModelUser->first_name);
        $this->assertSame('Bing', $viewModelUser->last_name);
        $this->assertSame('Chan', $viewModelUser->nickname);
        $this->assertSame('chandler@example.com', $viewModelUser->email);
        $this->assertSame('en_US', $viewModelUser->locale);
    }

    #[Test]
    public function it_tests_the_urls(): void
    {
        $viewModel = new SettingsIndexViewModel($this->createUser());
        $url = $viewModel->url();

        $this->assertSame(config('app.url').'/vaults', $url->dashboard);
        $this->assertSame(config('app.url').'/settings/profile', $url->profileUpdate);
        $this->assertSame(config('app.url').'/settings/logs', $url->logs);
    }
}
