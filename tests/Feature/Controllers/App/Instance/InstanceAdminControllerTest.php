<?php

declare(strict_types=1);

use App\Enums\PermissionEnum;
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
        'instance-admin/reviews',
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

it('says that support tickets and reviews are not built yet', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)->get('instance-admin/support')->assertOk()->assertSee('Soon');
    $this->actingAs($monica)->get('instance-admin/reviews')->assertOk()->assertSee('Soon');
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
