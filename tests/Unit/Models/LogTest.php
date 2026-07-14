<?php

declare(strict_types=1);
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $log = Log::factory()->create();

    expect($log->user()->exists())->toBeTrue();
});
it('gets the name of the user', function () {
    $user = User::factory()->create([
        'first_name' => 'Ross',
        'last_name' => 'Geller',
    ]);
    $log = Log::factory()->create([
        'user_id' => $user->id,
        'user_name' => 'Joey Tribbiani',
    ]);

    expect($log->getUserName())->toEqual('Ross Geller');

    $log->user_id = null;
    $log->save();

    expect($log->refresh()->getUserName())->toEqual('Joey Tribbiani');
});
