<?php

declare(strict_types=1);

use App\Actions\RecordPurchaseConsent;
use App\Enums\PermissionEnum;
use App\Enums\PurchaseConsentChoice;
use App\Models\Catalog;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * Every page of the panel, so the guard can be asserted over the whole surface
 * rather than one route at a time.
 */
function instanceAdminPages(): array
{
    return [
        'instance-admin',
        'instance-admin/accounts',
        'instance-admin/support',
        'instance-admin/marketing/testimonials',
        'instance-admin/site-options',
    ];
}

it('shows the overview to an instance administrator', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $response = $this->actingAs($monica)->get('instance-admin');

    $response->assertOk();
    $response->assertViewIs('app.instance.index');
});

it('lists the accounts to an instance administrator', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $this->createAccount('Central Perk');

    $response = $this->actingAs($monica)->get('instance-admin/accounts');

    $response->assertOk();
    $response->assertViewIs('app.instance.accounts.index');
});

it('filters the accounts by the email of a member', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $this->createUser([
        'account_id' => $centralPerk->id,
        'email' => 'ross@friends.com',
    ]);

    $response = $this->actingAs($monica)->get('instance-admin/accounts?search=ross@friends.com');

    $response->assertOk();
    expect($response->viewData('accounts')->pluck('id')->all())->toBe([$centralPerk->id]);
});

it('requires the search and the role to describe the same person', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $this->createUser([
        'account_id' => $centralPerk->id,
        'email' => 'gunther@friends.com',
        'role' => PermissionEnum::Owner->value,
    ]);
    $this->createUser([
        'account_id' => $centralPerk->id,
        'email' => 'rachel@friends.com',
        'role' => PermissionEnum::Viewer->value,
    ]);

    // Rachel is a viewer here, so asking for owners called rachel finds nothing
    // even though the account does have an owner and does have Rachel in it.
    $response = $this->actingAs($monica)->get('instance-admin/accounts?search=rachel@friends.com&role=owner');

    $response->assertOk();
    expect($response->viewData('accounts')->pluck('id')->all())->toBe([]);
});

it('treats a wildcard in the search as a literal character', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $this->createUser([
        'account_id' => $centralPerk->id,
        'email' => 'phoebe@friends.com',
    ]);

    $response = $this->actingAs($monica)->get('instance-admin/accounts?search=%');

    $response->assertOk();
    expect($response->viewData('accounts')->pluck('id')->all())->toBe([]);
});

it('shows an account to an instance administrator', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');

    $response = $this->actingAs($monica)->get('instance-admin/accounts/'.$centralPerk->id);

    $response->assertOk();
    $response->assertViewIs('app.instance.accounts.show');
});

it('opens the marketing testimonials moderation screen', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)->get('instance-admin/marketing/testimonials')->assertOk()->assertSee('Testimonials');
});

it('hides every page from a user who does not administer the instance', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $rachel, account: $account, role: PermissionEnum::Owner->value);

    foreach (instanceAdminPages() as $page) {
        $this->actingAs($rachel)->get($page)->assertNotFound();
    }
});

it('deletes an account', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');

    $response = $this->actingAs($monica)->delete('instance-admin/accounts/'.$centralPerk->id);

    $response->assertRedirect(route('instanceAdmin.accounts.index', absolute: false));
    $this->assertModelMissing($centralPerk);
});

it('forbids a user who does not administer the instance from deleting an account', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $centralPerk = $this->createAccount('Central Perk');

    $response = $this->actingAs($rachel)->delete('instance-admin/accounts/'.$centralPerk->id);

    $response->assertNotFound();
    $this->assertModelExists($centralPerk);
});

it('deletes a user', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Owner->value,
    ]);
    $ross = $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Editor->value,
    ]);

    $response = $this->actingAs($monica)->delete('instance-admin/users/'.$ross->id);

    $response->assertRedirect(route('instanceAdmin.accounts.show', $centralPerk->id, absolute: false));
    $this->assertModelMissing($ross);
});

it('grants the instance administration to a user', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $ross = $this->createUser([
        'account_id' => $centralPerk->id,
        'is_instance_administrator' => false,
    ]);

    $response = $this->actingAs($monica)->put('instance-admin/users/'.$ross->id.'/administrator', [
        'is_instance_administrator' => true,
    ]);

    $response->assertRedirect(route('instanceAdmin.accounts.show', $centralPerk->id, absolute: false));
    expect($ross->refresh()->isInstanceAdministrator())->toBeTrue();
});

it('forbids an administrator from deleting their own user', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $response = $this->actingAs($monica)->delete('instance-admin/users/'.$monica->id);

    $response->assertNotFound();
    $this->assertModelExists($monica);
});

it('reports the plan standing of a hosted account', function () {
    config(['pricing.hosted' => true]);

    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $catalog = Catalog::factory()->create(['account_id' => $centralPerk->id]);
    Item::factory()->count(15)->create(['catalog_id' => $catalog->id]);

    $this->actingAs($monica)
        ->get('instance-admin/accounts/'.$centralPerk->id)
        ->assertOk()
        ->assertSee('Free plan')
        ->assertSee('15 of 10 free, 15 hard limit');
});

it('reports an unlocked account as unlocked', function () {
    config(['pricing.hosted' => true]);

    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $centralPerk->update(['unlocked_at' => now()]);

    $this->actingAs($monica)
        ->get('instance-admin/accounts/'.$centralPerk->id)
        ->assertOk()
        ->assertSee('Unlocked on '.$centralPerk->unlocked_at->isoFormat('ll'))
        ->assertSee('0, uncapped');
});

it('says a self hosted instance applies no limit', function () {
    config(['pricing.hosted' => false]);

    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');

    $this->actingAs($monica)
        ->get('instance-admin/accounts/'.$centralPerk->id)
        ->assertOk()
        ->assertSee('Self hosted instance, no limit applies');
});

it('lists what an account confirmed before paying, with who, when and from where', function () {
    config(['pricing.hosted' => true]);

    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $rachel = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $this->assignUserToAccount(user: $rachel, account: $centralPerk, role: PermissionEnum::Owner->value);

    new RecordPurchaseConsent(
        user: $rachel,
        account: $centralPerk,
        ipAddress: '198.51.100.7',
    )->execute();

    $response = $this->actingAs($monica)
        ->get('instance-admin/accounts/'.$centralPerk->id)
        ->assertOk()
        ->assertSee('Purchase confirmations')
        ->assertSee('Rachel Green')
        ->assertSee('198.51.100.7');

    foreach (PurchaseConsentChoice::cases() as $choice) {
        $response->assertSee($choice->summary());
    }
});

it('says so when an account has confirmed nothing', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');

    $this->actingAs($monica)
        ->get('instance-admin/accounts/'.$centralPerk->id)
        ->assertOk()
        ->assertSee('Nobody in this account has gone through the purchase confirmation yet.');
});

it('no longer lists the billing plan as unsupported', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');

    $this->actingAs($monica)
        ->get('instance-admin/accounts/'.$centralPerk->id)
        ->assertOk()
        ->assertSee('Not supported yet')
        ->assertDontSee('Billing plan');
});
