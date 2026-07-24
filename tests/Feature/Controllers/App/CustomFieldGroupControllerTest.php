<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('adds a blank group to the type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->post('/settings/types/'.$type->id.'/groups');

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    expect($type->customFieldGroups()->count())->toBe(1);
});

it('renames a group', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Main']);

    $response = $this->actingAs($user)->put('/settings/types/'.$type->id.'/groups/'.$group->id, [
        'name' => 'Publishing info',
    ]);

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    expect($group->refresh()->name)->toBe('Publishing info');
});

it('adds a field into a group', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    $response = $this->actingAs($user)->post('/settings/types/'.$type->id.'/groups/'.$group->id.'/fields');

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    expect($group->customFields()->count())->toBe(1);
    expect($group->customFields()->first()->type_id)->toBe($type->id);
});

it('removes a group but keeps its fields as standalone', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $group->id]);

    $this->actingAs($user)->delete('/settings/types/'.$type->id.'/groups/'.$group->id)
        ->assertRedirect('/settings/types/'.$type->id.'/edit');

    $this->assertModelMissing($group);
    $this->assertModelExists($field);
    expect($field->refresh()->group_id)->toBeNull();
});

it('renders the groups and the standalone fields on the edit page', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Publishing info']);
    CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $group->id, 'name' => 'Issue #']);
    CustomField::factory()->create(['type_id' => $type->id, 'group_id' => null, 'name' => 'Notes']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit')
        ->assertOk()
        ->assertSee('Field groups')
        ->assertSee('Publishing info')
        ->assertSee('Standalone fields')
        ->assertSee('Issue #')
        ->assertSee('Notes');
});

it('shows the empty states when the type has no groups', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit')
        ->assertOk()
        ->assertSee('No field groups yet. Add one to organize related fields together.');
});

it('cannot touch a group of another accounts type', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreignType = CatalogType::factory()->create();
    $foreignGroup = CustomFieldGroup::factory()->create(['type_id' => $foreignType->id]);

    $this->actingAs($user)->delete('/settings/types/'.$foreignType->id.'/groups/'.$foreignGroup->id)->assertNotFound();
    $this->assertModelExists($foreignGroup);
});

it('cannot touch a group belonging to another type of the same account', function () {
    Queue::fake();

    $user = $this->createUser();
    $comics = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $wine = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $wine->id]);

    $this->actingAs($user)->delete('/settings/types/'.$comics->id.'/groups/'.$group->id)->assertNotFound();
    $this->assertModelExists($group);
});

it('forbids a viewer from adding a group', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post('/settings/types/'.$type->id.'/groups')->assertNotFound();
    expect($type->customFieldGroups()->count())->toBe(0);
});
