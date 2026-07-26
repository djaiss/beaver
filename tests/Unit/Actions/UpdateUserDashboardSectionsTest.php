<?php

declare(strict_types=1);

use App\Actions\UpdateUserDashboardSections;
use App\Enums\DashboardSectionEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('remembers which sections the user turned off', function (): void {
    $user = User::factory()->create();

    $updated = new UpdateUserDashboardSections(
        user: $user,
        hidden: [DashboardSectionEnum::Loans->value, DashboardSectionEnum::Locations->value],
    )->execute();

    expect($updated)->toBeInstanceOf(User::class)
        ->and($user->fresh()->hidden_dashboard_sections)->toBe(['loans', 'locations']);
});

it('turns every section back on with an empty list', function (): void {
    $user = User::factory()->create(['hidden_dashboard_sections' => ['loans']]);

    new UpdateUserDashboardSections(user: $user, hidden: [])->execute();

    expect($user->fresh()->hidden_dashboard_sections)->toBe([]);
});

it('stores a section only once', function (): void {
    $user = User::factory()->create();

    new UpdateUserDashboardSections(user: $user, hidden: ['loans', 'loans'])->execute();

    expect($user->fresh()->hidden_dashboard_sections)->toBe(['loans']);
});

it('refuses a section that does not exist', function (): void {
    $user = User::factory()->create();

    expect(fn (): User => new UpdateUserDashboardSections(user: $user, hidden: ['central-perk'])->execute())
        ->toThrow(ValidationException::class);
});
