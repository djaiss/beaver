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

it('sends guests to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

it('sends signed in users to the dashboard when the marketing site is off', function () {
    config()->set('marketing.show', false);
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('dashboard.index'));
});
