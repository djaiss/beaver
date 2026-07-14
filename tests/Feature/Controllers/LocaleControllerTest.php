<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

it('can update locale', function () {
    $response = $this->from('/')
        ->put('/locale', [
            'locale' => 'de_DE',
        ]);

    $response->assertRedirect('/');
    expect(session('locale'))->toEqual('de_DE');
    expect(App::getLocale())->toEqual('de_DE');
});
it('updates authenticated user locale', function () {
    $user = $this->createUser([
        'locale' => 'en',
    ]);

    $response = $this->actingAs($user)
        ->from('/')
        ->put('/locale', [
            'locale' => 'de_DE',
        ]);

    $response->assertRedirect('/');
    expect(session('locale'))->toEqual('de_DE');
    expect(App::getLocale())->toEqual('de_DE');
    expect($user->fresh()->locale)->toEqual('de_DE');
});
