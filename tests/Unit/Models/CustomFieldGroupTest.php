<?php

declare(strict_types=1);
use App\Models\CollectionType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to a collection type', function () {
    $type = CollectionType::factory()->create();
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    expect($group->collectionType()->exists())->toBeTrue();
    expect($group->collectionType->id)->toBe($type->id);
});

it('has many custom fields', function () {
    $group = CustomFieldGroup::factory()->create();
    CustomField::factory()->create(['type_id' => $group->type_id, 'group_id' => $group->id]);

    expect($group->customFields()->exists())->toBeTrue();
});

it('encrypts the name at rest', function () {
    $group = CustomFieldGroup::factory()->create(['name' => 'Publishing info']);

    $this->assertDatabaseMissing('custom_field_groups', ['name' => 'Publishing info']);
    expect($group->fresh()->name)->toBe('Publishing info');
});

it('is reachable from the type, and separates the grouped fields from the standalone ones', function () {
    $type = CollectionType::factory()->create();
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $group->id, 'name' => 'Grade']);
    CustomField::factory()->create(['type_id' => $type->id, 'group_id' => null, 'name' => 'Notes']);

    expect($type->customFieldGroups()->count())->toBe(1);
    expect($type->customFields()->count())->toBe(2);
    expect($type->ungroupedCustomFields()->get()->map->name->all())->toBe(['Notes']);
});

it('leaves a field ungrouped by default', function () {
    $field = CustomField::factory()->create();

    expect($field->group_id)->toBeNull();
    expect($field->group)->toBeNull();
});
