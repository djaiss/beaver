<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('displays the account page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->get('/settings/user');

    $response->assertOk();
    $response->assertViewIs('app.settings.user.index');
});
it('deletes the account', function () {
    Queue::fake();
    Mail::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->delete('/settings/user', [
            'feedback' => 'I no longer need this service',
        ]);

    $response->assertRedirect('/login');
    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
