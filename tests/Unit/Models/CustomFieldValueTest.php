<?php

declare(strict_types=1);
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an item', function () {
    $item = Item::factory()->create();
    $value = CustomFieldValue::factory()->create(['item_id' => $item->id]);

    expect($value->item)->toBeInstanceOf(Item::class);
    expect($value->item->id)->toBe($item->id);
});

it('belongs to a custom field', function () {
    $field = CustomField::factory()->create();
    $value = CustomFieldValue::factory()->create(['custom_field_id' => $field->id]);

    expect($value->customField)->toBeInstanceOf(CustomField::class);
    expect($value->customField->id)->toBe($field->id);
});

it('encrypts the value at rest', function () {
    $value = CustomFieldValue::factory()->create(['value' => 'Marvel']);

    $raw = DB::table('custom_field_values')->where('id', $value->id)->value('value');

    $this->assertNotSame('Marvel', $raw);
    expect(decrypt($raw, false))->toBe('Marvel');
});
