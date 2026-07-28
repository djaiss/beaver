<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\PurchaseConsentChoice;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\PurchaseConsent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Every box on the confirmation form, ticked.
 *
 * @return array<string, string>
 */
function everyConfirmation(): array
{
    $payload = [];

    foreach (PurchaseConsentChoice::cases() as $choice) {
        $payload[$choice->value] = '1';
    }

    return $payload;
}

function ownerOfHostedAccount(int $items = 12, string $role = PermissionEnum::Owner->value): User
{
    config(['pricing.hosted' => true]);

    $account = test()->createAccount();
    $user = test()->createUser();
    test()->assignUserToAccount(user: $user, account: $account, role: $role);

    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    Item::factory()->count($items)->create(['catalog_id' => $catalog->id]);

    return $user->refresh();
}

it('is not found on a self hosted instance', function () {
    config(['pricing.hosted' => false]);

    $user = $this->createUser();
    $this->assignUserToAccount(user: $user, account: $this->createAccount(), role: PermissionEnum::Owner->value);

    $this->actingAs($user)->get(route('upgrade.confirm.new'))->assertNotFound();
});

it('shows the confirmation to an owner', function () {
    $owner = ownerOfHostedAccount();

    $response = $this->actingAs($owner)
        ->get(route('upgrade.confirm.new'))
        ->assertOk()
        ->assertSee('This purchase is final. There are no refunds.');

    foreach (PurchaseConsentChoice::cases() as $choice) {
        $response->assertSee('name="'.$choice->value.'"', escape: false);
    }
});

it('is refused to an editor, on the form and on the submission', function () {
    $editor = ownerOfHostedAccount(role: PermissionEnum::Editor->value);

    // The owner middleware answers 403 here, the way it does on every other
    // owner only web route. RecordPurchaseConsent refuses independently of it.
    $this->actingAs($editor)->get(route('upgrade.confirm.new'))->assertForbidden();
    $this->actingAs($editor)->post(route('upgrade.confirm.create'), everyConfirmation())->assertForbidden();

    $this->assertDatabaseCount('purchase_consents', 0);
});

it('records every choice when all the boxes are ticked', function () {
    $owner = ownerOfHostedAccount();

    $this->actingAs($owner)
        ->post(route('upgrade.confirm.create'), everyConfirmation())
        ->assertRedirect(route('upgrade.confirm.new'))
        ->assertSessionHas('status', 'Your confirmations were recorded');

    expect(PurchaseConsent::query()->count())->toBe(count(PurchaseConsentChoice::cases()));

    $consent = PurchaseConsent::query()->firstOrFail();

    expect($consent->account_id)->toBe($owner->account_id);
    expect($consent->user_id)->toBe($owner->id);
    expect($consent->ip_address)->not->toBeNull();
});

it('refuses a submission with a box left unticked', function () {
    $owner = ownerOfHostedAccount();

    $payload = everyConfirmation();
    unset($payload[PurchaseConsentChoice::NoChargeback->value]);

    $this->actingAs($owner)
        ->post(route('upgrade.confirm.create'), $payload)
        ->assertSessionHasErrors(PurchaseConsentChoice::NoChargeback->value);

    $this->assertDatabaseCount('purchase_consents', 0);
});

it('refuses an empty submission', function () {
    $owner = ownerOfHostedAccount();

    $this->actingAs($owner)
        ->post(route('upgrade.confirm.create'), [])
        ->assertSessionHasErrors(array_column(PurchaseConsentChoice::cases(), 'value'));

    $this->assertDatabaseCount('purchase_consents', 0);
});

it('keeps the ticked boxes after a refused submission', function () {
    $owner = ownerOfHostedAccount();

    $payload = everyConfirmation();
    unset($payload[PurchaseConsentChoice::UnlockCovers->value]);

    $this->actingAs($owner)->post(route('upgrade.confirm.create'), $payload);

    $this->actingAs($owner)
        ->get(route('upgrade.confirm.new'))
        ->assertOk()
        ->assertSee('x-data="{ ticked: 3 }"', escape: false);
});
