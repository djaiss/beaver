<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Collection;
use App\Models\CustomField;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $type = Type::factory()->create(['account_id' => $account->id]);

    expect($type->account)->toBeInstanceOf(Account::class);
    expect($type->account->id)->toBe($account->id);
});

it('has many custom fields', function () {
    $type = Type::factory()->create();
    CustomField::factory()->create(['type_id' => $type->id]);

    expect($type->customFields()->exists())->toBeTrue();
    expect($type->customFields()->first())->toBeInstanceOf(CustomField::class);
});

it('belongs to many collections', function () {
    $type = Type::factory()->create();
    $collection = Collection::factory()->create();
    $type->collections()->attach($collection->id);

    expect($type->collections()->exists())->toBeTrue();
    expect($type->collections()->first())->toBeInstanceOf(Collection::class);
});

it('encrypts the name at rest', function () {
    $type = Type::factory()->create(['name' => 'Comics']);

    $rawName = DB::table('types')->where('id', $type->id)->value('name');

    $this->assertNotSame('Comics', $rawName);
    expect($type->fresh()->name)->toBe('Comics');
});
