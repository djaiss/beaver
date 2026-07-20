<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the pricing page', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.pricing.index'));

    $response
        ->assertOk()
        ->assertSee('almost un-be-leaf-able.')
        ->assertSee('Two ways in. Both fair.')
        ->assertSee('The Dam Accurate Pricing Calculator');
});

it('puts the price in perspective with the comparison grid', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.pricing.index'));

    $response
        ->assertOk()
        ->assertSee('What else is forty-nine bucks?')
        ->assertSee('4 fancy oat-milk lattes')
        ->assertSee('Zero monthly renewals');
});

it('offers to sign up when the visitor is a guest', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.pricing.index'));

    $response
        ->assertOk()
        ->assertSee('Get started')
        ->assertSee(route('register'));
});

it('offers to go back to the account when the visitor is signed in', function () {
    config()->set('marketing.show', true);
    $user = $this->createUser();

    $response = $this->actingAs($user)->get(route('marketing.pricing.index'));

    $response
        ->assertOk()
        ->assertSee('Go to your account')
        ->assertSee(route('dashboard.index'));
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.pricing.index'))->assertRedirect(route('login'));
});
