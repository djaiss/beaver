<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the account collection types', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Publisher']);

    $response = $this->actingAs($user)->get('/settings/types');

    $response->assertOk();
    $response->assertSee('Comics');
    $response->assertSee('Publisher');
});

it('does not list another accounts types', function () {
    $user = $this->createUser();
    CollectionType::factory()->create(['name' => 'Foreign type']);

    $response = $this->actingAs($user)->get('/settings/types');

    $response->assertOk();
    $response->assertDontSee('Foreign type');
});

it('forbids viewers from listing types', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->get('/settings/types')->assertNotFound();
});

it('allows an editor to list types', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Comics']);

    $this->actingAs($editor)->get('/settings/types')
        ->assertOk()
        ->assertSee('Comics');
});

it('creates a blank type and redirects to its edit page', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/settings/types');

    $type = CollectionType::query()->first();
    expect($type)->not->toBeNull();
    expect($type->account_id)->toBe($user->account_id);
    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
});

it('shows the edit page', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Vinyl Records']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit')
        ->assertOk()
        ->assertSee('Vinyl Records')
        ->assertSee('Custom fields')
        ->assertSee('Edit name')
        ->assertSee('saved automatically in real time');
});

it('cannot edit another accounts type', function () {
    $user = $this->createUser();
    $foreign = CollectionType::factory()->create();

    $this->actingAs($user)->get('/settings/types/'.$foreign->id.'/edit')->assertNotFound();
});

it('updates the name and color', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->put('/settings/types/'.$type->id, [
        'name' => 'Trading Cards',
        'color' => '#34D399',
    ]);

    $response->assertRedirect('/settings/types/'.$type->id.'/edit');
    $response->assertSessionHas('status', 'Type updated');

    $type->refresh();
    expect($type->name)->toBe('Trading Cards');
    expect($type->color)->toBe('#34D399');
});

it('validates the color when updating', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->put('/settings/types/'.$type->id, [
        'name' => 'Trading Cards',
        'color' => 'not-a-color',
    ])->assertSessionHasErrors('color');
});

it('deletes a type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->delete('/settings/types/'.$type->id);

    $response->assertRedirect('/settings/types');
    $this->assertModelMissing($type);
});

it('cannot delete another accounts type', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreign = CollectionType::factory()->create();

    $this->actingAs($user)->delete('/settings/types/'.$foreign->id)->assertNotFound();
    $this->assertModelExists($foreign);
});
