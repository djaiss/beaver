<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

it('shows the language switcher on the login page', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('name="locale"', false)
        ->assertSee(__('French'));
});

it('shows the language switcher on the register page', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee('name="locale"', false)
        ->assertSee(__('French'));
});

it('lets a guest switch the language and renders the login page in it', function () {
    $this->from('/login')
        ->put('/locale', ['locale' => 'fr_FR'])
        ->assertRedirect('/login');

    expect(session('locale'))->toBe('fr_FR');

    App::setLocale('en');

    $this->get('/login')
        ->assertOk()
        ->assertSee(__('Language', [], 'fr_FR'));
});

it('rejects an unsupported locale', function () {
    $this->from('/login')
        ->put('/locale', ['locale' => 'xx_XX'])
        ->assertSessionHasErrors('locale');
});
