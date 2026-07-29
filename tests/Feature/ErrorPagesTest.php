<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends a signed out visitor to the public site from the 404 page', function () {
    $response = $this->get('/this-page-does-not-exist');

    $response->assertNotFound();
    $response->assertSee(route('marketing.index'), false);
    $response->assertSee(route('marketing.docs.portal.home.show'), false);
    $response->assertSee(__('Pricing'));
});

it('sends a signed in user to the app from the 404 page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/collections/does-not-exist');

    $response->assertNotFound();
    $response->assertSee(route('dashboard.index'), false);
    $response->assertSee(route('collections.index'), false);
    $response->assertDontSee(route('marketing.index'), false);
});
