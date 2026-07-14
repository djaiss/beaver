<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('shows a valid invitation', function () {
    $invitation = Invitation::factory()->create();

    $response = $this->get("invitations/{$invitation->token}");

    $response->assertOk();
    $response->assertViewIs('app.account.invitations.show');
    $response->assertViewHas('invitation');
});
it('shows an expired invitation', function () {
    $invitation = Invitation::factory()->expired()->create();

    $response = $this->get("invitations/{$invitation->token}");

    $response->assertOk();
    expect($invitation->isPending())->toBeFalse();
});
it('returns not found for an unknown token', function () {
    $response = $this->get('invitations/unknown-token');

    $response->assertNotFound();
});
it('registers and accepts a guest with a new email', function () {
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

    $response->assertRedirect(route('dashboard.index', absolute: false));
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'phoebe.buffay@friends.com')->firstOrFail();
    expect($user->account_id)->toBe($account->id);
    expect($user->role)->toBe(PermissionEnum::Viewer->value);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});
it('redirects a guest to login when the email already has a user', function () {
    $this->createUser(['email' => 'chandler.bing@friends.com']);
    $invitation = Invitation::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post("invitations/{$invitation->token}/accept");

    $response->assertRedirect(route('login', absolute: false));
});
it('redirects a logged in user to login', function () {
    $account = $this->createAccount();
    $user = $this->createUser(['email' => 'ross.geller@friends.com']);
    $invitation = Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'ross.geller@friends.com',
    ]);

    $response = $this->actingAs($user)->post("invitations/{$invitation->token}/accept");

    $response->assertRedirect(route('login', absolute: false));
});
