<?php

declare(strict_types=1);
use App\Enums\PurchaseConsentChoice;
use App\Models\PurchaseConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an account and to a user', function () {
    $consent = PurchaseConsent::factory()->create();

    expect($consent->account()->exists())->toBeTrue();
    expect($consent->user()->exists())->toBeTrue();
});

it('casts the choice to its enum and the moment to a date', function () {
    $consent = PurchaseConsent::factory()->create(['choice' => PurchaseConsentChoice::NoChargeback]);

    expect($consent->choice)->toBe(PurchaseConsentChoice::NoChargeback);
    expect($consent->accepted_at)->toBeInstanceOf(Carbon\Carbon::class);
});

it('reads the name off the user while they exist', function () {
    $consent = PurchaseConsent::factory()->create(['user_name' => 'Stale Name']);
    $consent->user->update(['first_name' => 'Monica', 'last_name' => 'Geller']);

    expect($consent->fresh()->getUserName())->toBe('Monica Geller');
});

it('falls back to the recorded name once the user is gone', function () {
    $consent = PurchaseConsent::factory()->create(['user_name' => 'Monica Geller']);
    $consent->user->delete();

    $consent = $consent->fresh();

    expect($consent->user)->toBeNull();
    expect($consent->getUserName())->toBe('Monica Geller');
});

it('gives every choice a translated label and a plain summary', function () {
    foreach (PurchaseConsentChoice::cases() as $choice) {
        expect($choice->label())->not->toBe('');
        expect($choice->summary())->not->toBe('');
    }
});
