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

it('populates each type with its custom fields in order', function () {
    $account = Account::factory()->create();

    new PopulateAccount($account)->handle();

    $comics = $account->collectionTypes()->get()->firstWhere('name', 'Comics');

    $fields = $comics->customFields()->orderBy('position')->get();

    expect($fields)->toHaveCount(7);
    expect($fields->map->name->all())->toBe([
        'Issue #',
        'Publisher',
        'Writer',
        'Artist',
        'Cover Date',
        'Variant',
        'Signed',
    ]);
    expect($fields->pluck('position')->all())->toBe([1, 2, 3, 4, 5, 6, 7]);
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
