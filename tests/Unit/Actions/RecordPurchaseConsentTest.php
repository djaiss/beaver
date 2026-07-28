<?php

declare(strict_types=1);
use App\Actions\RecordPurchaseConsent;
use App\Enums\PermissionEnum;
use App\Enums\PurchaseConsentChoice;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\PurchaseConsent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('records one row per choice', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $consents = new RecordPurchaseConsent(
        user: $owner,
        account: $account,
        ipAddress: '198.51.100.7',
    )->execute();

    expect($consents)->toHaveCount(count(PurchaseConsentChoice::cases()));

    foreach (PurchaseConsentChoice::cases() as $choice) {
        $this->assertDatabaseHas('purchase_consents', [
            'account_id' => $account->id,
            'user_id' => $owner->id,
            'choice' => $choice->value,
        ]);
    }
});

it('records the name, the address and the moment of the confirmation', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new RecordPurchaseConsent(
        user: $owner,
        account: $account,
        ipAddress: '198.51.100.7',
    )->execute();

    $consent = PurchaseConsent::query()->firstOrFail();

    expect($consent->user_name)->toBe('Monica Geller');
    expect($consent->ip_address)->toBe('198.51.100.7');
    expect($consent->accepted_at->timestamp)->toEqualWithDelta(now()->timestamp, 5);

    // The name and the address are encrypted at rest, so neither is readable in the row.
    expect($consent->getRawOriginal('user_name'))->not->toBe('Monica Geller');
    expect($consent->getRawOriginal('ip_address'))->not->toBe('198.51.100.7');
});

it('accepts a request that carried no address', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new RecordPurchaseConsent(user: $owner, account: $account)->execute();

    expect(PurchaseConsent::query()->firstOrFail()->ip_address)->toBeNull();
});

it('gives every row of one submission the same moment', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new RecordPurchaseConsent(user: $owner, account: $account)->execute();

    expect(PurchaseConsent::query()->pluck('accepted_at')->unique())->toHaveCount(1);
});

it('logs the confirmation', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new RecordPurchaseConsent(user: $owner, account: $account)->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::PurchaseConsentRecorded);
});

it('refuses an editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    expect(fn () => new RecordPurchaseConsent(user: $editor, account: $account)->execute())
        ->toThrow(ModelNotFoundException::class);

    $this->assertDatabaseCount('purchase_consents', 0);
});

it('refuses an owner of another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $other = $this->createAccount('Geller Collectibles');
    $outsider = $this->createUser();
    $this->assignUserToAccount(user: $outsider, account: $other, role: PermissionEnum::Owner->value);

    expect(fn () => new RecordPurchaseConsent(user: $outsider, account: $account)->execute())
        ->toThrow(ModelNotFoundException::class);

    $this->assertDatabaseCount('purchase_consents', 0);
});
