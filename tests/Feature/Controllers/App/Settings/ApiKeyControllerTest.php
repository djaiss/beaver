<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a new api token', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->from('/settings/security/create')
        ->post('/settings/api-keys', [
            'label' => 'My API Token',
        ]);

    $response->assertRedirect('/settings/security');
    $response->assertSessionHas('status', 'API key created');
});
it('can delete an api token', function () {
    $user = $this->createUser();
    $token = $user->createToken('Test API Token');

    $response = $this->actingAs($user)
        ->delete('/settings/api-keys/'.$token->accessToken->id);

    $response->assertRedirect('/settings/security');
    $response->assertSessionHas('status', 'API key deleted');
});
it('returns not found when deleting an unknown api token', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->delete('/settings/api-keys/9999');

    $response->assertNotFound();
});
