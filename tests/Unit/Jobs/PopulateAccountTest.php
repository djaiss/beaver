<?php

declare(strict_types=1);
use App\Enums\FieldTypeEnum;
use App\Jobs\PopulateAccount;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('populates the account with the default collection types', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $types = $account->collectionTypes()->get();

    expect($types)->toHaveCount(12);
    expect($types->map->name->all())->toContain('Comics', 'Vinyl Records', 'CD', 'DVD', 'Wine');
});

it('populates each type with its field groups in order', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $comics = $account->collectionTypes()->get()->firstWhere('name', 'Comics');

    $groups = $comics->customFieldGroups()->orderBy('position')->get();

    expect($groups)->toHaveCount(2);
    expect($groups->map->name->all())->toBe(['Publishing info', 'Condition & grading']);
    expect($groups->pluck('position')->all())->toBe([1, 2]);
});

it('populates each group with its custom fields in order', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $comics = $account->collectionTypes()->get()->firstWhere('name', 'Comics');
    $group = $comics->customFieldGroups()->get()->firstWhere('name', 'Publishing info');

    $fields = $group->customFields()->orderBy('position')->get();

    expect($fields->map->name->all())->toBe([
        'Issue #',
        'Publisher',
        'Writer',
        'Artist',
        'Cover Date',
    ]);

    // A position orders a field within its group, so each group restarts at 1.
    expect($fields->pluck('position')->all())->toBe([1, 2, 3, 4, 5]);

    // The field still knows the type it belongs to, alongside its group.
    expect($fields->every(fn ($field): bool => $field->type_id === $comics->id))->toBeTrue();
});

it('leaves a type with no groups holding every field as standalone', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    // Wine mixes the two: Bottle Size sits outside of any group.
    $wine = $account->collectionTypes()->get()->firstWhere('name', 'Wine');

    expect($wine->ungroupedCustomFields()->get()->map->name->all())->toBe(['Bottle Size']);
    expect($wine->customFieldGroups()->get()->map->name->all())->toBe(['Origin']);
    expect($wine->customFields()->count())->toBe(5);
});

it('stores the options and field type of a select field', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $comics = $account->collectionTypes()->get()->firstWhere('name', 'Comics');
    $publisher = $comics->customFields()->get()->firstWhere('name', 'Publisher');

    expect($publisher->field_type)->toBe(FieldTypeEnum::Select);
    expect($publisher->options)->toBe(['Marvel', 'DC', 'Image', 'Dark Horse', 'Independent']);

    $coins = $account->collectionTypes()->get()->firstWhere('name', 'Coins');
    $grade = $coins->customFields()->get()->firstWhere('name', 'Grade');

    expect($grade->options)->toContain('MS-70', 'PR-1');
});

it('encrypts the type and field names at rest', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $type = $account->collectionTypes()->first();

    $this->assertDatabaseMissing('types', ['name' => 'Comics']);
    $this->assertDatabaseMissing('custom_fields', ['name' => 'Issue #']);
    expect($type->name)->toBe('Comics');
    expect($type->color)->toStartWith('#');
});

it('populates the account with the default locations', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $locations = $account->locations()->get();

    expect($locations)->toHaveCount(5);
    expect($locations->map->name->all())->toBe([
        'Living Room',
        'Storage',
        'Display Case',
        'Garage',
        'Office',
    ]);
    expect($locations->every(fn ($location): bool => $location->parent_id === null))->toBeTrue();
    expect($locations->firstWhere('name', 'Living Room')->emoji)->toBe('🛋️');
});

it('populates the account with the default conditions', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $conditions = $account->conditions()->get();

    expect($conditions)->toHaveCount(5);
    expect($conditions->map->name->all())->toBe([
        'New',
        'Like New',
        'Used',
        'Worn',
        'Damaged',
    ]);
});
