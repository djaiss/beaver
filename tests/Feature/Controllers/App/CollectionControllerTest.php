<?php

declare(strict_types=1);
use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Enums\VisibilityEnum;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\CollectionView;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the account collections', function () {
    $user = $this->createUser();
    Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);
    Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Vinyl — Jazz LPs']);

    $response = $this->actingAs($user)->get('/collections');

    $response->assertOk();
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Vinyl — Jazz LPs');
});

it('does not list another accounts collections', function () {
    $user = $this->createUser();
    Collection::factory()->create(['name' => 'Foreign Collection']);

    $response = $this->actingAs($user)->get('/collections');

    $response->assertOk();
    $response->assertDontSee('Foreign Collection');
});

it('allows a viewer to list collections', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Collection::factory()->create(['account_id' => $account->id, 'name' => 'Wine Cellar']);

    $this->actingAs($viewer)->get('/collections')
        ->assertOk()
        ->assertSee('Wine Cellar');
});

it('shows an empty state when there are no collections', function () {
    $user = $this->createUser();

    $this->actingAs($user)->get('/collections')
        ->assertOk()
        ->assertSee('No collections yet');
});

it('shows the new collection form', function () {
    $user = $this->createUser();
    CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $response = $this->actingAs($user)->get('/collections/new');

    $response->assertOk();
    $response->assertSee('Create a collection');
    $response->assertSee('Comics');
});

it('forbids viewers from viewing the new collection form', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->get('/collections/new')->assertNotFound();
});

it('creates a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $response = $this->actingAs($user)->post('/collections', [
        'name' => 'Marvel Comics 1990s',
        'description' => 'My run of 90s Marvel',
        'emoji' => '📚',
        'visibility' => VisibilityEnum::Shared->value,
        'currency' => 'USD',
        'collection_type_ids' => [$type->id],
    ]);

    $response->assertRedirect('/collections');
    $response->assertSessionHas('status', 'Collection created');

    $collection = Collection::query()->first();
    expect($collection)->not->toBeNull();
    expect($collection->name)->toBe('Marvel Comics 1990s');
    expect($collection->account_id)->toBe($user->account_id);
    expect($collection->collectionTypes->pluck('id')->all())->toBe([$type->id]);
});

it('validates the name is required', function () {
    $user = $this->createUser();

    $this->actingAs($user)->post('/collections', [
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertSessionHasErrors('name');
});

it('rejects an invalid visibility value', function () {
    $user = $this->createUser();

    $this->actingAs($user)->post('/collections', [
        'name' => 'Wine Cellar',
        'visibility' => 'secret',
    ])->assertSessionHasErrors('visibility');
});

it('forbids viewers from creating a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->post('/collections', [
        'name' => 'Wine Cellar',
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertNotFound();
});

it('shows the edit collection form', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);
    $collection->collectionTypes()->attach($type->id);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/edit');

    $response->assertOk();
    $response->assertSee('Edit collection');
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Comics');
});

it('cannot edit another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->get('/collections/'.$foreign->id.'/edit')->assertNotFound();
});

it('forbids viewers from viewing the edit collection form', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/collections/'.$collection->id.'/edit')->assertNotFound();
});

it('updates a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Private->value,
    ]);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $response = $this->actingAs($user)->put('/collections/'.$collection->id, [
        'name' => 'Marvel Comics 1990s',
        'description' => 'My run of 90s Marvel',
        'emoji' => '📚',
        'visibility' => VisibilityEnum::Shared->value,
        'currency' => 'USD',
        'collection_type_ids' => [$type->id],
    ]);

    $response->assertRedirect('/collections/'.$collection->id);
    $response->assertSessionHas('status', 'Collection updated');

    $collection->refresh();
    expect($collection->name)->toBe('Marvel Comics 1990s');
    expect($collection->description)->toBe('My run of 90s Marvel');
    expect($collection->emoji)->toBe('📚');
    expect($collection->visibility)->toBe(VisibilityEnum::Shared);
    expect($collection->currency)->toBe('USD');
    expect($collection->collectionTypes->pluck('id')->all())->toBe([$type->id]);
});

it('unlinks the types left unchecked when updating', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $collection->collectionTypes()->attach($type->id);

    $this->actingAs($user)->put('/collections/'.$collection->id, [
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Shared->value,
    ]);

    expect($collection->fresh()->collectionTypes)->toBeEmpty();
});

it('does not link a type of another account when updating', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $foreignType = CollectionType::factory()->create();

    $this->actingAs($user)->put('/collections/'.$collection->id, [
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Shared->value,
        'collection_type_ids' => [$foreignType->id],
    ]);

    expect($collection->fresh()->collectionTypes)->toBeEmpty();
});

it('validates the name is required when updating', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->put('/collections/'.$collection->id, [
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertSessionHasErrors('name');
});

it('cannot update another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->put('/collections/'.$foreign->id, [
        'name' => 'Hijacked',
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertNotFound();
});

it('forbids viewers from updating a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->put('/collections/'.$collection->id, [
        'name' => 'Wine Cellar',
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertNotFound();
});

it('deletes a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->delete('/collections/'.$collection->id);

    $response->assertRedirect('/collections');
    $response->assertSessionHas('status', 'Collection deleted');
    $this->assertSoftDeleted($collection);
});

it('cannot delete another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->delete('/collections/'.$foreign->id)->assertNotFound();
});

it('forbids viewers from deleting a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->delete('/collections/'.$collection->id)->assertNotFound();
});

it('offers edit and delete from the collection page', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get('/collections/'.$collection->id)
        ->assertOk()
        ->assertSee('Add item')
        ->assertSee('Edit collection')
        ->assertSee('Delete collection')
        ->assertSee('/collections/'.$collection->id.'/edit');
});

it('does not offer edit and delete to a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/collections/'.$collection->id)
        ->assertOk()
        ->assertDontSee('Edit collection')
        ->assertDontSee('Delete collection');
});

it('shows a collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id);

    $response->assertOk();
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Est. value');
});

it('shows the grid (sidebar) chrome by default', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id);

    $response->assertOk();
    $response->assertSee('Back to collections');
    $response->assertDontSee('Filter by location');
});

// Alpine only takes over the display once it has booted, so the wrong view would paint first
// unless the server hides it up front. See the flicker on first load of a remembered list view.
it('hides the grid on first paint when the remembered view is the list', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man #1']);
    CollectionView::factory()->create([
        'user_id' => $user->id,
        'collection_id' => $collection->id,
        'items_view' => ItemViewEnum::List->value,
    ]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id);

    $response->assertOk();
    $response->assertSee('<div x-show="view === \'grid\'" style="display: none;">', false);
    $response->assertSee('<div x-show="view === \'list\'" style="">', false);
});

it('hides the list on first paint when the remembered view is the grid', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man #1']);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id);

    $response->assertOk();
    $response->assertSee('<div x-show="view === \'grid\'" style="">', false);
    $response->assertSee('<div x-show="view === \'list\'" style="display: none;">', false);
});

it('shows the table (top bar) chrome when the user last used it', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    CollectionView::factory()->create([
        'user_id' => $user->id,
        'collection_id' => $collection->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id);

    $response->assertOk();
    $response->assertSee('Filter by location');
    $response->assertDontSee('Back to collections');
});

it('remembers the view for each user independently', function () {
    $account = $this->createAccount();
    $ross = $this->createUser(['account_id' => $account->id]);
    $rachel = $this->createUser(['account_id' => $account->id]);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    CollectionView::factory()->create([
        'user_id' => $ross->id,
        'collection_id' => $collection->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);

    $this->actingAs($ross)->get('/collections/'.$collection->id)->assertSee('Filter by location');
    $this->actingAs($rachel)->get('/collections/'.$collection->id)->assertDontSee('Filter by location');
});

it('allows a viewer to see a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id, 'name' => 'Wine Cellar']);

    $this->actingAs($viewer)->get('/collections/'.$collection->id)
        ->assertOk()
        ->assertSee('Wine Cellar');
});

it('cannot see another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->get('/collections/'.$foreign->id)->assertNotFound();
});

it('renders an item main photo when there is one', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true]);

    $this->actingAs($user)->get('/collections/'.$collection->id)
        ->assertOk()
        ->assertSee(route('items.photos.show', $photo), false);
});
