<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocaleControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_update_locale(): void
    {
        $response = $this->from('/')
            ->put('/locale', [
                'locale' => 'de_DE',
            ]);

        $response->assertRedirect('/');
        $this->assertEquals('de_DE', session('locale'));
        $this->assertEquals('de_DE', App::getLocale());
    }

    #[Test]
    public function it_updates_authenticated_user_locale(): void
    {
        $user = $this->createUser([
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)
            ->from('/')
            ->put('/locale', [
                'locale' => 'de_DE',
            ]);

        $response->assertRedirect('/');
        $this->assertEquals('de_DE', session('locale'));
        $this->assertEquals('de_DE', App::getLocale());
        $this->assertEquals('de_DE', $user->fresh()->locale);
    }
}
