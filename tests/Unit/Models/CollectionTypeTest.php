<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    expect($collectionType->account)->toBeInstanceOf(Account::class);
    expect($collectionType->account->id)->toBe($account->id);
});

it('has many custom fields', function () {
    $collectionType = CollectionType::factory()->create();
    CustomField::factory()->create(['type_id' => $collectionType->id]);

    expect($collectionType->customFields()->exists())->toBeTrue();
    expect($collectionType->customFields()->first())->toBeInstanceOf(CustomField::class);
});

it('belongs to many collections', function () {
    $collectionType = CollectionType::factory()->create();
    $collection = Collection::factory()->create();
    $collectionType->collections()->attach($collection->id);

    expect($collectionType->collections()->exists())->toBeTrue();
    expect($collectionType->collections()->first())->toBeInstanceOf(Collection::class);
});

it('encrypts the name at rest', function () {
    $collectionType = CollectionType::factory()->create(['name' => 'Comics']);

    $rawName = DB::table('types')->where('id', $collectionType->id)->value('name');

    $this->assertNotSame('Comics', $rawName);
    expect($collectionType->fresh()->name)->toBe('Comics');
});
