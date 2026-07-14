<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks the health of the application', function () {
    $response = $this->json('GET', '/api/health');

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'ok',
        'services' => [
            'database' => 'up',
        ],
    ]);
});
