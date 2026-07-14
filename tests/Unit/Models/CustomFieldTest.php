<?php

declare(strict_types=1);
use App\Enums\FieldTypeEnum;
use App\Models\CustomField;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a type', function () {
    $type = Type::factory()->create();
    $customField = CustomField::factory()->create(['type_id' => $type->id]);

    expect($customField->type)->toBeInstanceOf(Type::class);
    expect($customField->type->id)->toBe($type->id);
});

it('casts the field type to an enum', function () {
    $customField = CustomField::factory()->create(['field_type' => FieldTypeEnum::Select->value]);

    expect($customField->fresh()->field_type)->toBe(FieldTypeEnum::Select);
});

it('casts the options to an array', function () {
    $customField = CustomField::factory()->create(['options' => ['Mint', 'Near Mint', 'Good']]);

    expect($customField->fresh()->options)->toBe(['Mint', 'Near Mint', 'Good']);
});

it('casts the position to an integer', function () {
    $customField = CustomField::factory()->create(['position' => 3]);

    expect($customField->fresh()->position)->toBe(3);
});

it('encrypts the name at rest', function () {
    $customField = CustomField::factory()->create(['name' => 'Issue #']);

    $rawName = DB::table('custom_fields')->where('id', $customField->id)->value('name');

    $this->assertNotSame('Issue #', $rawName);
    expect($customField->fresh()->name)->toBe('Issue #');
});
