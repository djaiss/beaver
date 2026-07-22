<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays the security page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->get('/profile/security');

    $response->assertStatus(200);
    $response->assertViewIs('app.settings.security.index');
    $response->assertViewHas('apiKeys');
    $response->assertViewHas('has2fa');
});

it('renders the section title help popovers on the security page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/profile/security');

    $response->assertOk();
    $response->assertSee('Rotate your password deliberately');
    $response->assertSee('Adds a second step to signing in');
    $response->assertSee('deletes your own user automatically');
    $response->assertSee('Personal tokens that let a script or app act as you');
});
