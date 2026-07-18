<?php

declare(strict_types=1);
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the export page of a type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Publishing info']);
    CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $group->id, 'name' => 'Publisher', 'field_type' => FieldTypeEnum::Text->value]);

    $response = $this->actingAs($user)->get('/settings/types/'.$type->id.'/export');

    $response->assertOk();
    $response->assertSee('Export Comics');
    $response->assertSee('Publishing info');
    $response->assertSee('Publisher');
    $response->assertSee('schemaVersion');
    $response->assertSee('comics.type.json');
});

it('links to the export page from the edit screen', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit')
        ->assertOk()
        ->assertSee('Export as JSON')
        ->assertSee('/settings/types/'.$type->id.'/export');
});

it('does not export another accounts type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['name' => 'Foreign type']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/export')->assertNotFound();
});

it('forbids viewers from exporting a type', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/settings/types/'.$type->id.'/export')->assertNotFound();
});

it('allows an editor to export a type', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Vinyl']);

    $this->actingAs($editor)->get('/settings/types/'.$type->id.'/export')
        ->assertOk()
        ->assertSee('vinyl.type.json');
});

it('falls back to a generic file name when the name does not slug', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => '📦']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/export')
        ->assertOk()
        ->assertSee('type.type.json');
});

it('returns a 404 when the type does not exist', function () {
    $user = $this->createUser();

    $this->actingAs($user)->get('/settings/types/999999/export')->assertNotFound();
});
