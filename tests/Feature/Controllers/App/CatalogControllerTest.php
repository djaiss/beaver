<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Enums\VisibilityEnum;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\CatalogView;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('lists the account collections', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Vinyl — Jazz LPs']);

    $response = $this->actingAs($user)->get('/collections');

    $response->assertOk();
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Vinyl — Jazz LPs');
});

it('does not list another accounts collections', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['name' => 'Foreign Catalog']);

    $response = $this->actingAs($user)->get('/collections');

    $response->assertOk();
    $response->assertDontSee('Foreign Catalog');
});

it('allows a viewer to list collections', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Catalog::factory()->create(['account_id' => $account->id, 'name' => 'Wine Cellar']);

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
    CatalogType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $response = $this->actingAs($user)->get('/collections/new');

    $response->assertOk();
    $response->assertSee('Create a collection');
    $response->assertSee('Comics');
});

it('renders the types, visibility and currency help popovers on the new collection form', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/collections/new');

    $response->assertOk();
    $response->assertSee('Enabling a type lets items in this collection use its custom fields');
    $response->assertSee('who this collection is meant for');
    $response->assertSee('overriding the account default');
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
    $type = CatalogType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

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

    $catalog = Catalog::query()->first();
    expect($catalog)->not->toBeNull();
    expect($catalog->name)->toBe('Marvel Comics 1990s');
    expect($catalog->account_id)->toBe($user->account_id);
    expect($catalog->catalogTypes->pluck('id')->all())->toBe([$type->id]);
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
    $type = CatalogType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);
    $catalog->catalogTypes()->attach($type->id);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/edit');

    $response->assertOk();
    $response->assertSee('Edit collection');
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Comics');
});

it('renders the types, visibility and currency help popovers on the edit collection form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id.'/edit');

    $response->assertOk();
    $response->assertSee('Enabling a type lets items in this collection use its custom fields');
    $response->assertSee('who this collection is meant for');
    $response->assertSee('overriding the account default');
});

it('cannot edit another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();

    $this->actingAs($user)->get('/collections/'.$foreign->id.'/edit')->assertNotFound();
});

it('forbids viewers from viewing the edit collection form', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/collections/'.$catalog->id.'/edit')->assertNotFound();
});

it('updates a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Private->value,
    ]);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $response = $this->actingAs($user)->put('/collections/'.$catalog->id, [
        'name' => 'Marvel Comics 1990s',
        'description' => 'My run of 90s Marvel',
        'emoji' => '📚',
        'visibility' => VisibilityEnum::Shared->value,
        'currency' => 'USD',
        'collection_type_ids' => [$type->id],
    ]);

    $response->assertRedirect('/collections/'.$catalog->id);
    $response->assertSessionHas('status', 'Collection updated');

    $catalog->refresh();
    expect($catalog->name)->toBe('Marvel Comics 1990s');
    expect($catalog->description)->toBe('My run of 90s Marvel');
    expect($catalog->emoji)->toBe('📚');
    expect($catalog->visibility)->toBe(VisibilityEnum::Shared);
    expect($catalog->currency)->toBe('USD');
    expect($catalog->catalogTypes->pluck('id')->all())->toBe([$type->id]);
});

it('unlinks the types left unchecked when updating', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $catalog->catalogTypes()->attach($type->id);

    $this->actingAs($user)->put('/collections/'.$catalog->id, [
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Shared->value,
    ]);

    expect($catalog->fresh()->catalogTypes)->toBeEmpty();
});

it('does not link a type of another account when updating', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $foreignType = CatalogType::factory()->create();

    $this->actingAs($user)->put('/collections/'.$catalog->id, [
        'name' => 'Marvel Comics',
        'visibility' => VisibilityEnum::Shared->value,
        'collection_type_ids' => [$foreignType->id],
    ]);

    expect($catalog->fresh()->catalogTypes)->toBeEmpty();
});

it('validates the name is required when updating', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->put('/collections/'.$catalog->id, [
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertSessionHasErrors('name');
});

it('cannot update another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();

    $this->actingAs($user)->put('/collections/'.$foreign->id, [
        'name' => 'Hijacked',
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertNotFound();
});

it('forbids viewers from updating a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->put('/collections/'.$catalog->id, [
        'name' => 'Wine Cellar',
        'visibility' => VisibilityEnum::Shared->value,
    ])->assertNotFound();
});

it('deletes a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->delete('/collections/'.$catalog->id);

    $response->assertRedirect('/collections');
    $response->assertSessionHas('status', 'Collection deleted');
    $this->assertSoftDeleted($catalog);
});

it('cannot delete another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();

    $this->actingAs($user)->delete('/collections/'.$foreign->id)->assertNotFound();
});

it('forbids viewers from deleting a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->delete('/collections/'.$catalog->id)->assertNotFound();
});

it('offers edit and delete from the collection page', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get('/collections/'.$catalog->id)
        ->assertOk()
        ->assertSee('Add item')
        ->assertSee('Edit collection')
        ->assertSee('Delete collection')
        ->assertSee('/collections/'.$catalog->id.'/edit');
});

it('does not offer edit and delete to a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/collections/'.$catalog->id)
        ->assertOk()
        ->assertDontSee('Edit collection')
        ->assertDontSee('Delete collection');
});

it('shows a collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id);

    $response->assertOk();
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Est. value');
});

it('paginates the items 1000 at a time', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->count(30)->create(['catalog_id' => $catalog->id]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id);

    $response->assertOk();
    expect($response->viewData('items')->perPage())->toBe(1000);
    expect($response->viewData('items')->lastPage())->toBe(1);
    expect($response->viewData('items'))->toHaveCount(30);
});

it('shows the grid (sidebar) chrome by default', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id);

    $response->assertOk();
    $response->assertSee('Back to collections');
    $response->assertDontSee('Filter by location');
});

// Alpine only takes over the display once it has booted, so the wrong view would paint first
// unless the server hides it up front. See the flicker on first load of a remembered list view.
it('hides the grid on first paint when the remembered view is the list', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1']);
    CatalogView::factory()->create([
        'user_id' => $user->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::List->value,
    ]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id);

    $response->assertOk();
    $response->assertSee('<div x-show="view === \'grid\'" style="display: none;">', false);
    $response->assertSee('<div x-show="view === \'list\'" style="">', false);
});

it('hides the list on first paint when the remembered view is the grid', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1']);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id);

    $response->assertOk();
    $response->assertSee('<div x-show="view === \'grid\'" style="">', false);
    $response->assertSee('<div x-show="view === \'list\'" style="display: none;">', false);
});

it('shows the table (top bar) chrome when the user last used it', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    CatalogView::factory()->create([
        'user_id' => $user->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);

    $response = $this->actingAs($user)->get('/collections/'.$catalog->id);

    $response->assertOk();
    $response->assertSee('Filter by location');
    $response->assertDontSee('Back to collections');
});

it('remembers the view for each user independently', function () {
    $account = $this->createAccount();
    $ross = $this->createUser(['account_id' => $account->id]);
    $rachel = $this->createUser(['account_id' => $account->id]);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    CatalogView::factory()->create([
        'user_id' => $ross->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);

    $this->actingAs($ross)->get('/collections/'.$catalog->id)->assertSee('Filter by location');
    $this->actingAs($rachel)->get('/collections/'.$catalog->id)->assertDontSee('Filter by location');
});

it('allows a viewer to see a collection', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'name' => 'Wine Cellar']);

    $this->actingAs($viewer)->get('/collections/'.$catalog->id)
        ->assertOk()
        ->assertSee('Wine Cellar');
});

it('cannot see another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();

    $this->actingAs($user)->get('/collections/'.$foreign->id)->assertNotFound();
});

it('renders an item main photo when there is one', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true]);

    $this->actingAs($user)->get('/collections/'.$catalog->id)
        ->assertOk()
        ->assertSee(route('items.photos.show', $photo), false);
});

it('shows the avatar of the collection author when they have one', function () {
    Storage::fake();

    $user = $this->createUser();

    new UpdateUserAvatar(
        user: $user,
        file: UploadedFile::fake()->image('ross.jpg', 400, 400),
    )->execute();

    Catalog::factory()->create([
        'account_id' => $user->account_id,
        'created_by_id' => $user->id,
        'created_by_name' => 'Ross Geller',
    ]);

    $response = $this->actingAs($user->fresh())->get(route('collections.index'));

    $response->assertOk();
    $response->assertSee(route('profile.avatar.show', ['user' => $user, 'size' => 32]), escape: false);
});

it('falls back to the initials of the collection author when they have no avatar', function () {
    $user = $this->createUser();

    Catalog::factory()->create([
        'account_id' => $user->account_id,
        'created_by_id' => $user->id,
        'created_by_name' => 'Ross Geller',
    ]);

    $response = $this->actingAs($user)->get(route('collections.index'));

    $response->assertOk();
    $response->assertSee('RG');
});
