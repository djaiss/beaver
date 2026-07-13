<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Account;

use App\Enums\PermissionEnum;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_a_valid_invitation(): void
    {
        $invitation = Invitation::factory()->create();

        $response = $this->get("invitations/{$invitation->token}");

        $response->assertOk();
        $response->assertViewIs('app.account.invitations.show');
        $response->assertViewHas('invitation');
    }

    #[Test]
    public function it_shows_an_expired_invitation(): void
    {
        $invitation = Invitation::factory()->expired()->create();

        $response = $this->get("invitations/{$invitation->token}");

        $response->assertOk();
        $this->assertFalse($invitation->isPending());
    }

    #[Test]
    public function it_returns_not_found_for_an_unknown_token(): void
    {
        $response = $this->get('invitations/unknown-token');

        $response->assertNotFound();
    }

    #[Test]
    public function it_lets_a_logged_in_invited_user_accept(): void
    {
        Queue::fake();

        $account = $this->createAccount();
        $user = $this->createUser(['email' => 'ross.geller@friends.com']);
        $invitation = Invitation::factory()->create([
            'account_id' => $account->id,
            'email' => 'ross.geller@friends.com',
            'role' => PermissionEnum::Editor->value,
        ]);

        $response = $this->actingAs($user)->post("invitations/{$invitation->token}/accept");

        $response->assertRedirect(route('accounts.show', $account->id, absolute: false));
        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => PermissionEnum::Editor->value,
        ]);
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    #[Test]
    public function it_registers_and_accepts_a_guest_with_a_new_email(): void
    {
        Queue::fake();

        $account = $this->createAccount();
        $invitation = Invitation::factory()->create([
            'account_id' => $account->id,
            'email' => 'phoebe.buffay@friends.com',
            'role' => PermissionEnum::Viewer->value,
        ]);

        $response = $this->post("invitations/{$invitation->token}/accept", [
            'first_name' => 'Phoebe',
            'last_name' => 'Buffay',
            'password' => '5UTHSmdj',
            'password_confirmation' => '5UTHSmdj',
        ]);

        $response->assertRedirect(route('accounts.show', $account->id, absolute: false));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'phoebe.buffay@friends.com')->firstOrFail();
        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => PermissionEnum::Viewer->value,
        ]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login_when_the_email_already_has_a_user(): void
    {
        $this->createUser(['email' => 'chandler.bing@friends.com']);
        $invitation = Invitation::factory()->create([
            'email' => 'chandler.bing@friends.com',
        ]);

        $response = $this->post("invitations/{$invitation->token}/accept");

        $response->assertRedirect(route('login', absolute: false));
    }
}
