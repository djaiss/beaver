<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\Collection;
use App\Models\Invitation;
use App\Models\Type;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('defaults its currency to USD', function () {
    $account = Account::query()->create(['name' => 'Central Perk']);

    expect($account->fresh()->currency_code)->toBe('USD');
});

it('has many users', function () {
    $account = $this->createAccount();
    $user = $this->createUser();
    $this->assignUserToAccount(user: $user, account: $account);

    expect($account->users()->exists())->toBeTrue();
    expect($account->users()->first())->toBeInstanceOf(User::class);
});

it('has many invitations', function () {
    $account = $this->createAccount();
    Invitation::factory()->create(['account_id' => $account->id]);

    expect($account->invitations()->exists())->toBeTrue();
    expect($account->invitations()->first())->toBeInstanceOf(Invitation::class);
});

it('has many collections', function () {
    $account = $this->createAccount();
    Collection::factory()->create(['account_id' => $account->id]);

    expect($account->collections()->exists())->toBeTrue();
    expect($account->collections()->first())->toBeInstanceOf(Collection::class);
});

it('has many types', function () {
    $account = $this->createAccount();
    Type::factory()->create(['account_id' => $account->id]);

    expect($account->types()->exists())->toBeTrue();
    expect($account->types()->first())->toBeInstanceOf(Type::class);
});

it('lists only owners as administrators', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $administrators = $account->administrators()->get();

    expect($administrators)->toHaveCount(1);
    expect($administrators->contains('id', $owner->id))->toBeTrue();
    expect($administrators->contains('id', $viewer->id))->toBeFalse();
});

it('knows whether a user is a member', function () {
    $account = $this->createAccount();
    $member = $this->createUser();
    $stranger = $this->createUser();
    $this->assignUserToAccount(user: $member, account: $account);

    expect($account->hasMember($member))->toBeTrue();
    expect($account->hasMember($stranger))->toBeFalse();
});

it('returns the role for a member', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $stranger = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    expect($account->roleFor($owner))->toBe(PermissionEnum::Owner->value);
    expect($account->roleFor($stranger))->toBeNull();
});

it('encrypts the name at rest', function () {
    $account = $this->createAccount(name: 'Central Perk');

    $rawName = DB::table('accounts')->where('id', $account->id)->value('name');

    $this->assertNotSame('Central Perk', $rawName);
    expect($account->name)->toBe('Central Perk');
    expect($account->fresh()->name)->toBe('Central Perk');
});
