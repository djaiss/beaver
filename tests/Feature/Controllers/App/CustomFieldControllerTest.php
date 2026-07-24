<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
use App\Models\CustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('adds a blank field to the type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->post('/settings/types/'.$type->id.'/fields');

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    expect($type->customFields()->count())->toBe(1);
});

it('updates a field and stores the options of a select', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);

    $response = $this->actingAs($user)->put('/settings/types/'.$type->id.'/fields/'.$field->id, [
        'name' => 'Grade',
        'field_type' => 'select',
        'options' => ['CGC 9.8', 'Raw'],
    ]);

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');

    $field->refresh();
    expect($field->name)->toBe('Grade');
    expect($field->field_type->value)->toBe('select');
    expect($field->options)->toBe(['CGC 9.8', 'Raw']);
    expect($field->position)->toBe(1);
});

it('filters out the blank trailing option when saving a select', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => 'select', 'position' => 1]);

    // The editor always submits an empty trailing "add option" input, which the
    // framework converts to null; it must not blow up validation.
    $response = $this->actingAs($user)->put('/settings/types/'.$type->id.'/fields/'.$field->id, [
        'name' => 'Grade',
        'field_type' => 'select',
        'options' => ['CGC 9.6', 'Raw', ''],
    ]);

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    expect($field->fresh()->options)->toBe(['CGC 9.6', 'Raw']);
});

it('clears the options when the field is no longer a select', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
        'field_type' => 'select',
        'options' => ['A'],
        'position' => 1,
    ]);

    $this->actingAs($user)->put('/settings/types/'.$type->id.'/fields/'.$field->id, [
        'name' => 'Publisher',
        'field_type' => 'text',
        'options' => ['A'],
    ]);

    expect($field->refresh()->options)->toBeNull();
});

it('validates the field type', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id]);

    $this->actingAs($user)->put('/settings/types/'.$type->id.'/fields/'.$field->id, [
        'field_type' => 'bogus',
    ])->assertSessionHasErrors('field_type');
});

it('removes a field', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id]);

    $this->actingAs($user)->delete('/settings/types/'.$type->id.'/fields/'.$field->id)
        ->assertRedirect('/settings/types/'.$type->id.'/edit');

    $this->assertModelMissing($field);
});

it('cannot touch a field of another accounts type', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreignType = CatalogType::factory()->create();
    $foreignField = CustomField::factory()->create(['type_id' => $foreignType->id]);

    $this->actingAs($user)->delete('/settings/types/'.$foreignType->id.'/fields/'.$foreignField->id)->assertNotFound();
    $this->assertModelExists($foreignField);
});

it('forbids a viewer from adding a field', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post('/settings/types/'.$type->id.'/fields')->assertNotFound();
    expect($type->customFields()->count())->toBe(0);
});
