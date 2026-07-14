<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a new api token', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->from('/profile/security/create')
        ->post('/profile/api-keys', [
            'label' => 'My API Token',
        ]);

    $response->assertRedirect('/profile/security');
    $response->assertSessionHas('status', 'API key created');
});

it('can delete an api token', function () {
    $user = $this->createUser();
    $token = $user->createToken('Test API Token');

    $response = $this->actingAs($user)
        ->delete('/profile/api-keys/'.$token->accessToken->id);

    $response->assertRedirect('/profile/security');
    $response->assertSessionHas('status', 'API key deleted');
});

it('returns not found when deleting an unknown api token', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->delete('/profile/api-keys/9999');

    $response->assertNotFound();
});
