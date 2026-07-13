<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\User;
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
    public function it_creates_a_user_with_their_own_account(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'email' => 'chandler.bing@friends.com',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('accounts.index', absolute: false));

        $user = User::query()->where('email', 'chandler.bing@friends.com')->firstOrFail();

        // A single account was created, owned by the new user.
        $this->assertCount(1, $user->accounts()->get());

        $account = $user->accounts()->firstOrFail();
        $this->assertSame(PermissionEnum::Owner->value, $account->pivot->role);
        $this->assertSame('Chandler Bing', $account->name);
        $this->assertSame($user->id, $account->created_by_id);
        $this->assertSame(1, Account::query()->count());
    }
}
