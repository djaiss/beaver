<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_create_account_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    #[Test]
    public function it_creates_an_account(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'email' => 'chandler.bing@friends.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('vault.index', absolute: false));
    }
}
