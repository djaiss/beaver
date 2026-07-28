<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

/*
 * A password manager decides what to offer from three attributes on the field:
 * autocomplete says whether this is an existing password or a new one, and so
 * whether to fill or to generate; passwordrules says what a generated one has to
 * satisfy; and data-1p-ignore, which x-input adds by default, tells 1Password to
 * skip the field entirely.
 *
 * These are easy to get wrong and invisible when they are, so the forms that ask
 * for a password assert them here. The rule advertised is the one the server
 * enforces, Password::min(8)->uncompromised(), so a generated password always
 * passes validation.
 */

/**
 * The attributes of one input on a rendered page, by its name.
 *
 * @return array<string, string>
 */
function inputAttributes(string $html, string $name): array
{
    preg_match_all('/<input[^>]*>/', $html, $matches);

    foreach ($matches[0] as $tag) {
        if (! str_contains($tag, 'name="'.$name.'"')) {
            continue;
        }

        preg_match_all('/([a-zA-Z0-9-]+)="([^"]*)"/', $tag, $pairs, PREG_SET_ORDER);

        $attributes = ['data-1p-ignore' => str_contains($tag, 'data-1p-ignore') ? 'yes' : 'no'];

        foreach ($pairs as $pair) {
            $attributes[$pair[1]] = $pair[2];
        }

        return $attributes;
    }

    return [];
}

it('renders autocomplete as a real attribute rather than an escaped string', function () {
    $html = $this->get(route('register'))->assertOk()->getContent();

    // The component used to build this by concatenating inside {{ }}, which HTML
    // escaped the quotes and left the browser with a token it could not read.
    expect($html)->toContain('autocomplete="new-password"');
    expect($html)->not->toContain('autocomplete=&quot;');
});

it('asks a password manager to generate a new password when registering', function () {
    $html = $this->get(route('register'))->assertOk()->getContent();

    foreach (['password', 'password_confirmation'] as $field) {
        $input = inputAttributes($html, $field);

        expect($input['autocomplete'])->toBe('new-password');
        expect($input['passwordrules'])->toBe('minlength: 8');
        expect($input['data-1p-ignore'])->toBe('no');
    }

    expect(inputAttributes($html, 'email')['autocomplete'])->toBe('username');
});

it('asks a password manager to fill the existing password when logging in', function () {
    $html = $this->get(route('login'))->assertOk()->getContent();

    $input = inputAttributes($html, 'password');

    // Signing in fills rather than generates, so no passwordrules here.
    expect($input['autocomplete'])->toBe('current-password');
    expect($input)->not->toHaveKey('passwordrules');
    expect($input['data-1p-ignore'])->toBe('no');
});

it('asks a password manager to generate a new password when resetting one', function () {
    $user = $this->createUser();
    $url = route('password.reset', ['token' => Password::createToken($user), 'email' => $user->email]);

    $html = $this->get($url)->assertOk()->getContent();

    foreach (['password', 'password_confirmation'] as $field) {
        $input = inputAttributes($html, $field);

        expect($input['autocomplete'])->toBe('new-password');
        expect($input['passwordrules'])->toBe('minlength: 8');
        expect($input['data-1p-ignore'])->toBe('no');
    }
});

it('asks a password manager to generate a new password when accepting an invitation', function () {
    $account = $this->createAccount();
    $invitation = Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'phoebe@friends.com',
    ]);

    $html = $this->get(route('invitations.show', $invitation->token))->assertOk()->getContent();

    foreach (['password', 'password_confirmation'] as $field) {
        $input = inputAttributes($html, $field);

        expect($input['autocomplete'])->toBe('new-password');
        expect($input['passwordrules'])->toBe('minlength: 8');
        expect($input['data-1p-ignore'])->toBe('no');
    }
});

it('asks a password manager to generate a new password when changing one', function () {
    $user = $this->createUser();
    $this->assignUserToAccount(user: $user, account: $this->createAccount());

    $html = $this->actingAs($user)->get(route('profile.security.index'))->assertOk()->getContent();

    expect(inputAttributes($html, 'current_password')['autocomplete'])->toBe('current-password');

    foreach (['new_password', 'new_password_confirmation'] as $field) {
        $input = inputAttributes($html, $field);

        expect($input['autocomplete'])->toBe('new-password');
        expect($input['passwordrules'])->toBe('minlength: 8');
        expect($input['data-1p-ignore'])->toBe('no');
    }
});

it('leaves the default in place, so an ordinary field is still skipped', function () {
    $user = $this->createUser();
    $this->assignUserToAccount(user: $user, account: $this->createAccount(), role: PermissionEnum::Editor->value);

    // x-input opts out of password managers unless a screen says otherwise, which
    // is what keeps the 1Password icon off every text box in the application.
    $html = $this->actingAs($user)->get(route('collections.new'))->assertOk()->getContent();

    expect(inputAttributes($html, 'name')['data-1p-ignore'])->toBe('yes');
});
