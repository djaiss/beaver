<?php

declare(strict_types=1);

use App\Enums\DashboardSectionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('remembers the sections a user hides', function (): void {
    $user = $this->createUser();

    $response = $this->actingAs($user)->putJson(route('dashboard.sections.update'), [
        'sections' => [DashboardSectionEnum::Loans->value],
    ]);

    $response->assertNoContent();

    expect($user->fresh()->hidden_dashboard_sections)->toBe(['loans']);
});

it('shows every section again when the list comes back empty', function (): void {
    $user = $this->createUser(['hidden_dashboard_sections' => ['loans', 'activity']]);

    $response = $this->actingAs($user)->putJson(route('dashboard.sections.update'), [
        'sections' => [],
    ]);

    $response->assertNoContent();

    expect($user->fresh()->hidden_dashboard_sections)->toBe([]);
});

it('rejects a section it does not know', function (): void {
    $user = $this->createUser();

    $response = $this->actingAs($user)->putJson(route('dashboard.sections.update'), [
        'sections' => ['central-perk'],
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('sections.0');
});

it('does not let a stranger set the preference', function (): void {
    $response = $this->putJson(route('dashboard.sections.update'), ['sections' => []]);

    $response->assertUnauthorized();
});
