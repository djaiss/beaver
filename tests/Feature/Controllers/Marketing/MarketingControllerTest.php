<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the marketing homepage', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('The collection manager that belongs to you.')
        ->assertSee('One app for every collection.')
        ->assertSee('https://github.com/djaiss/kollek');
});

it('offers to sign up when the visitor is a guest', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('Get started')
        ->assertSee(route('register'));
});

it('offers to go back to the account when the visitor is signed in', function () {
    config()->set('marketing.show', true);
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/');

    $response
        ->assertOk()
        ->assertSee('Go to your account')
        ->assertSee(route('dashboard.index'));
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get('/')->assertRedirect(route('login'));

    $user = $this->createUser();
    $this->actingAs($user)->get('/')->assertRedirect(route('login'));
});

it('carries signed in users on to their dashboard when the marketing site is off', function () {
    config()->set('marketing.show', false);
    $user = $this->createUser();

    // The login page sits behind the guest middleware, so it hands anyone already signed
    // in back to the application instead of leaving them on a redirect loop.
    $response = $this->actingAs($user)->followingRedirects()->get('/');

    $response->assertOk();
});
