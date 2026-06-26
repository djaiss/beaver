<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\EmailSent;
use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_settings_page(): void
    {
        $user = $this->createUser();

        Log::factory()->create([
            'user_id' => $user->id,
        ]);
        EmailSent::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/settings');

        $response->assertOk();
    }

    #[Test]
    public function it_updates_the_profile_information(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)
            ->put('/settings/profile', [
                'first_name' => 'Chandler',
                'last_name' => 'Bing',
                'nickname' => 'Chan',
                'email' => 'chandler.bing@friends.com',
                'locale' => 'de_DE',
                'time_format_24h' => 'true',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings');
    }
}
