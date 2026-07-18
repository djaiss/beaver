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
