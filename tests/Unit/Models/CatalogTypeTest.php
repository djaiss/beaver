<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\CustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    expect($catalogType->account)->toBeInstanceOf(Account::class);
    expect($catalogType->account->id)->toBe($account->id);
});

it('has many custom fields', function () {
    $catalogType = CatalogType::factory()->create();
    CustomField::factory()->create(['type_id' => $catalogType->id]);

    expect($catalogType->customFields()->exists())->toBeTrue();
    expect($catalogType->customFields()->first())->toBeInstanceOf(CustomField::class);
});

it('belongs to many collections', function () {
    $catalogType = CatalogType::factory()->create();
    $catalog = Catalog::factory()->create();
    $catalogType->catalogs()->attach($catalog->id);

    expect($catalogType->catalogs()->exists())->toBeTrue();
    expect($catalogType->catalogs()->first())->toBeInstanceOf(Catalog::class);
});

it('encrypts the name at rest', function () {
    $catalogType = CatalogType::factory()->create(['name' => 'Comics']);

    $rawName = DB::table('types')->where('id', $catalogType->id)->value('name');

    $this->assertNotSame('Comics', $rawName);
    expect($catalogType->fresh()->name)->toBe('Comics');
});
