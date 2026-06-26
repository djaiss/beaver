<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_all_the_logs(): void
    {
        Date::setTestNow(Date::create(2018, 1, 1));
        $user = $this->createUser();

        Log::factory()->create([
            'vault_id' => null,
            'user_id' => $user->id,
            'action' => 'log.user.profile_updated',
        ]);

        $response = $this->actingAs($user)
            ->get('/settings/logs');

        $response->assertOk();
        $response->assertViewIs('app.settings.logs.index');
        $response->assertViewHas('logs');
    }

    #[Test]
    public function it_shows_a_pagination(): void
    {
        $user = $this->createUser();

        Log::factory()
            ->count(15)
            ->create([
                'vault_id' => null,
                'user_id' => $user->id,
            ]);

        $response = $this->actingAs($user)
            ->get('/settings/logs');

        $response->assertOk();
        $this->assertCount(10, $response['logs']);

        $this->assertTrue($response['logs']->hasMorePages());
    }
}
