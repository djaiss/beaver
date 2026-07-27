<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the pricing page', function () {
    config()->set('marketing.show', true);

    $response = $this->get(route('marketing.pricing.index'));

    $response
        ->assertOk()
        ->assertSee('almost unbelievable.')
        ->assertSee('Two ways in. Both fair.')
        ->assertSee('The Suspiciously Accurate Pricing Calculator');
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

it('offers the same call to action to a signed in visitor', function () {
    // The page is cached as one copy for everybody, so it cannot ask who is
    // reading it.
    config()->set('marketing.show', true);
    $user = $this->createUser();

    $response = $this->actingAs($user)->get(route('marketing.pricing.index'));

    $response
        ->assertOk()
        ->assertSee('Get started')
        ->assertDontSee('Go to your account');
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.pricing.index'))->assertRedirect(route('login'));
});
