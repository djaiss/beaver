<?php

declare(strict_types=1);

use App\Models\CollectionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a type, renames it, and edits its fields inline', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = visit('/settings/types');
    $page->assertSee('Collection types');

    // Creating a type lands on its editor.
    $page->press('New type')
        ->assertSee('Custom fields');

    // Renaming happens behind the "Edit name" toggle and saves on its own.
    $page->press('Edit name')
        ->fill('#type-name-input', 'Comics')
        ->press('Save')
        ->assertSee('Type updated')
        ->assertSee('Comics');

    $type = CollectionType::query()->first();
    expect($type->name)->toBe('Comics');

    // Adding a field saves immediately.
    $page->click('[data-test="add-field-button"]')
        ->assertSee('Field added');

    expect($type->customFields()->count())->toBe(1);

    // Switching a field to "Select" auto-saves and reveals its options editor.
    $page->select('field_type', 'select')
        ->assertSee('Field updated')
        ->assertSee('Options');

    expect($type->customFields()->first()->field_type->value)->toBe('select');

    // Adding an option to the select field persists it.
    $page->fill('[data-test="option-draft"]', 'CGC 9.8')
        ->click('[data-test="add-option-button"]')
        ->assertSee('CGC 9.8');

    expect($type->customFields()->first()->options)->toBe(['CGC 9.8']);

    // ...and removing it persists too (the remove button confirms first).
    $page->script('window.confirm = () => true');
    $page->click('[data-test="remove-option-0"]')
        ->assertDontSee('CGC 9.8');

    expect($type->customFields()->first()->options)->toBe([]);
});

it('reaches the export page from the menu of the two part button', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $page = visit('/settings/types/'.$type->id.'/edit');

    $page->click('[data-test="export-type-button"]')
        ->assertSee('Export Comics')
        ->assertSee('schemaVersion')
        ->assertSee('comics.type.json');

    $page->assertNoSmoke();
});

it('keeps fields renameable and reorderable after adding more', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $page = visit('/settings/types/'.$type->id.'/edit');

    $page->click('[data-test="add-field-button"]')->assertSee('Field added');
    $page->click('[data-test="add-field-button"]')->assertSee('Field added');

    expect($type->customFields()->count())->toBe(2);

    $fields = $type->customFields()->orderBy('id')->get();
    $first = $fields[0];

    // A newly added field can still be renamed (committed with Enter).
    $page->type('[data-test="field-name-'.$first->id.'"]', 'Issue #')
        ->keys('[data-test="field-name-'.$first->id.'"]', 'Enter')
        ->assertSee('Field updated');

    expect($first->fresh()->name)->toBe('Issue #');

    // ...and reordered.
    $page->click('[data-test="move-down-'.$first->id.'"]')->assertSee('Field moved');

    expect($first->fresh()->position)->toBeGreaterThan($fields[1]->fresh()->position);

    $page->assertNoSmoke();
});

it('adds a group, puts a field in it, and keeps that field when the group goes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $page = visit('/settings/types/'.$type->id.'/edit');
    $page->assertSee('No field groups yet');

    // Adding a group saves immediately.
    $page->click('[data-test="add-group-button"]')->assertSee('Group added');

    $group = $type->customFieldGroups()->first();
    expect($group)->not->toBeNull();

    // Naming it auto-saves on blur.
    $page->type('[data-test="group-name-'.$group->id.'"]', 'Publishing info')
        ->keys('[data-test="group-name-'.$group->id.'"]', 'Enter')
        ->assertSee('Group updated');

    expect($group->fresh()->name)->toBe('Publishing info');

    // A field added from the group header lands inside it, not standalone.
    $page->click('[data-test="add-field-to-group-'.$group->id.'"]')->assertSee('Field added');

    $field = $type->customFields()->first();
    expect($field->group_id)->toBe($group->id);
    expect($type->ungroupedCustomFields()->count())->toBe(0);

    $page->type('[data-test="field-name-'.$field->id.'"]', 'Issue #')
        ->keys('[data-test="field-name-'.$field->id.'"]', 'Enter')
        ->assertSee('Field updated');

    // Deleting the group keeps the field: it drops back to standalone.
    $page->script('window.confirm = () => true');
    $page->click('[data-test="delete-group-'.$group->id.'"]')->assertSee('Group removed');

    expect($group->fresh())->toBeNull();
    expect($field->fresh()->group_id)->toBeNull();
    expect($field->fresh()->name)->toBe('Issue #');

    // The field is still on the page, now rendered as a standalone field.
    $page->assertSee('No field groups yet')
        ->assertPresent('[data-test="field-row-'.$field->id.'"]')
        ->assertValue('[data-test="field-name-'.$field->id.'"]', 'Issue #');

    $page->assertNoSmoke();
});

it('reorders the groups of a type', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $page = visit('/settings/types/'.$type->id.'/edit');

    $page->click('[data-test="add-group-button"]')->assertSee('Group added');
    $page->click('[data-test="add-group-button"]')->assertSee('Group added');

    $groups = $type->customFieldGroups()->orderBy('id')->get();
    $first = $groups[0];

    $page->click('[data-test="move-group-down-'.$first->id.'"]')->assertSee('Group moved');

    expect($first->fresh()->position)->toBeGreaterThan($groups[1]->fresh()->position);

    $page->assertNoSmoke();
});
