<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * A signed in member of a hosted account holding the given number of items.
 */
function memberOfHostedAccount(int $items = 0, string $role = PermissionEnum::Owner->value): User
{
    config(['pricing.hosted' => true]);

    $account = test()->createAccount();
    $user = test()->createUser();
    test()->assignUserToAccount(user: $user, account: $account, role: $role);

    if ($items > 0) {
        $catalog = Catalog::factory()->create(['account_id' => $account->id]);
        Item::factory()->count($items)->create(['catalog_id' => $catalog->id]);
    }

    return $user->refresh();
}

it('is not found on a self hosted instance', function () {
    config(['pricing.hosted' => false]);

    $user = $this->createUser();
    $this->assignUserToAccount(user: $user, account: $this->createAccount(), role: PermissionEnum::Owner->value);

    $this->actingAs($user)->get(route('upgrade.index'))->assertNotFound();
});

it('shows the plan to an owner', function () {
    $owner = memberOfHostedAccount(items: 12);

    $this->actingAs($owner)
        ->get(route('upgrade.index'))
        ->assertOk()
        ->assertSee('Your collection outgrew the free plan. Nice work.')
        ->assertSee(route('upgrade.confirm.new'));
});

it('shows the plan to a viewer without the way to pay', function () {
    $viewer = memberOfHostedAccount(items: 12, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)
        ->get(route('upgrade.index'))
        ->assertOk()
        ->assertSee('Only an owner of this account can unlock it. Ask one of them to take a look at this page.')
        ->assertDontSee(route('upgrade.confirm.new'));
});

it('reads sensibly for an account that has not filled the plan', function () {
    $owner = memberOfHostedAccount(items: 3);

    $this->actingAs($owner)
        ->get(route('upgrade.index'))
        ->assertOk()
        ->assertSee('3 of 10 free items used')
        ->assertDontSee('Your collection outgrew the free plan. Nice work.');
});

it('renders for a brand new account with no items at all', function () {
    $owner = memberOfHostedAccount();

    $this->actingAs($owner)->get(route('upgrade.index'))->assertOk();
});
