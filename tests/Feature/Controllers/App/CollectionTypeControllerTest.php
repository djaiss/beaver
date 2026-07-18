<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
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

it('keeps the actions menu out of the morph', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $response = $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit');

    $response->assertOk();

    // This screen refreshes itself with a Turbo morph, which reverts the display
    // Alpine sets on the menu and leaves it hanging open. data-morph-skip is what
    // keeps it closed, so the attribute has to stay on the menu itself.
    expect($response->getContent())->toMatch('/role="menu"[^>]*data-morph-skip|data-morph-skip[^>]*role="menu"/s');
});

it('offers rating as a field type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Publisher']);

    $response = $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit');

    $response->assertOk();
    $response->assertSee('value="rating"', false);
    $response->assertSee('Rating');
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

it('links to each collection using the type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Vinyl Records']);
    $linked = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Central Perk Vinyl']);
    $linked->collectionTypes()->attach($type->id);
    $unlinked = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Joeys Baywatch Tapes']);

    $response = $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit');

    $response->assertOk();

    // The collection using the type links through to it.
    $response->assertSee('Central Perk Vinyl');
    $response->assertSee('href="'.route('collections.show', $linked->id).'"', false);

    // A collection that does not use the type is no longer listed at all.
    $response->assertDontSee('Joeys Baywatch Tapes');
    $response->assertDontSee('href="'.route('collections.show', $unlinked->id).'"', false);
});

it('tells the user when no collection uses the type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Joeys Baywatch Tapes']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit')
        ->assertOk()
        ->assertSee('No collections use this type yet.')
        ->assertDontSee('Joeys Baywatch Tapes');
});

it('does not offer the collections of another account', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    Collection::factory()->create(['name' => 'Someone Elses Collection']);

    $this->actingAs($user)->get('/settings/types/'.$type->id.'/edit')
        ->assertOk()
        ->assertDontSee('Someone Elses Collection');
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
    $response->assertSessionHas('status_description', 'Your changes to the type were saved.');

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
