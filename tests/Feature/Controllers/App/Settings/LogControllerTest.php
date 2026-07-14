<?php

declare(strict_types=1);
use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

it('shows all the logs', function () {
    Date::setTestNow(Date::create(2018, 1, 1));
    $user = $this->createUser();

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => 'log.user.profile_updated',
    ]);

    $response = $this->actingAs($user)
        ->get('/profile/logs');

    $response->assertStatus(200);
    $response->assertViewIs('app.settings.logs.index');
    $response->assertViewHas('logs');
});
it('shows a pagination', function () {
    $user = $this->createUser();

    Log::factory()
        ->count(15)
        ->create([
            'user_id' => $user->id,
        ]);

    $response = $this->actingAs($user)
        ->get('/profile/logs');

    $response->assertStatus(200);
    expect($response['logs'])->toHaveCount(10);

    expect($response['logs']->hasMorePages())->toBeTrue();
});
