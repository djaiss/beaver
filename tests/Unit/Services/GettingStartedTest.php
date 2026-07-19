<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Invitation;
use App\Models\Location;
use App\Models\Tag;
use App\Services\GettingStarted;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reports every step as outstanding for a brand new account', function () {
    $account = $this->createAccount();

    $steps = new GettingStarted($account)->steps();

    expect($steps)->toHaveCount(5);
    expect($steps->pluck('key')->all())->toBe(['types', 'tags', 'members', 'locations', 'collection']);
    expect($steps->where('done', true))->toHaveCount(0);
    expect(new GettingStarted($account)->doneCount())->toBe(0);
});

it('does not count the seeded collection types as configured', function () {
    $account = $this->createAccount();

    // PopulateAccount creates the defaults without an author, the way the seeding job does.
    CollectionType::factory()->count(3)->create(['account_id' => $account->id, 'created_by_id' => null]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'types')['done'])->toBeFalse();
});

it('counts a collection type the user made themselves', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    CollectionType::factory()->create(['account_id' => $account->id, 'created_by_id' => null]);
    CollectionType::factory()->create(['account_id' => $account->id, 'created_by_id' => $owner->id]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'types')['done'])->toBeTrue();
});

it('does not count the seeded locations as configured', function () {
    $account = $this->createAccount();

    Location::factory()->count(5)->create(['account_id' => $account->id, 'created_by_id' => null]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'locations')['done'])->toBeFalse();
});

it('counts a location the user added themselves', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    Location::factory()->create(['account_id' => $account->id, 'created_by_id' => $owner->id]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'locations')['done'])->toBeTrue();
});

it('counts tags as configured once the account has one', function () {
    $account = $this->createAccount();

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'tags')['done'])->toBeFalse();

    Tag::factory()->create(['account_id' => $account->id]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'tags')['done'])->toBeTrue();
});

it('counts members as done once a second person is in the account', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'members')['done'])->toBeFalse();

    $second = $this->createUser();
    $this->assignUserToAccount(user: $second, account: $account, role: PermissionEnum::Editor->value);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'members')['done'])->toBeTrue();
});

it('counts members as done while an invitation is still pending', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    Invitation::factory()->create(['account_id' => $account->id, 'accepted_at' => null]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'members')['done'])->toBeTrue();
});

it('does not count an invitation that has already been accepted', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    Invitation::factory()->create(['account_id' => $account->id, 'accepted_at' => now()]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'members')['done'])->toBeFalse();
});

it('counts the collection step once the account has one', function () {
    $account = $this->createAccount();

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'collection')['done'])->toBeFalse();

    Collection::factory()->create(['account_id' => $account->id]);

    expect(new GettingStarted($account)->steps()->firstWhere('key', 'collection')['done'])->toBeTrue();
});

it('ignores the data of another account', function () {
    $account = $this->createAccount();
    $other = $this->createAccount('Other');

    Tag::factory()->create(['account_id' => $other->id]);
    Collection::factory()->create(['account_id' => $other->id]);

    expect(new GettingStarted($account)->doneCount())->toBe(0);
});

it('counts how many steps are behind the user', function () {
    $account = $this->createAccount();

    Tag::factory()->create(['account_id' => $account->id]);
    Collection::factory()->create(['account_id' => $account->id]);

    expect(new GettingStarted($account)->doneCount())->toBe(2);
});
