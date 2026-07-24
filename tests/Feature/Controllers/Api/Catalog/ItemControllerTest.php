<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\Category;
use App\Models\Copy;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Item;
use App\Models\Set;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'description',
            'catalog_id',
            'type_id',
            'category_id',
            'set_id',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the items of a collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Issue 1']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Issue 2']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/items')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list items from another account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create();
    Item::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/items')->assertNotFound();
});

it('shows an item', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Issue 1']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/items/'.$item->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'item')
        ->assertJsonPath('data.attributes.name', 'Issue 1')
        ->assertJsonPath('data.links.self', route('api.collections.items.show', [$catalog->id, $item->id]));
});

it('returns not found for an item from another collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/items/'.$item->id)->assertNotFound();
});

it('creates an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collections/'.$catalog->id.'/items', [
        'name' => 'Amazing Spider-Man #1',
        'description' => 'Near mint.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'Amazing Spider-Man #1')
        ->assertJsonPath('data.attributes.catalog_id', (string) $catalog->id);

    $item = Item::query()->latest('id')->first();
    expect($item->catalog_id)->toBe($catalog->id);
    expect($item->name)->toBe('Amazing Spider-Man #1');
});

it('validates the name when creating an item', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collections/'.$catalog->id.'/items', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts item creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/collections/'.$catalog->id.'/items', ['name' => 'Issue 1'])
        ->assertNotFound();
});

it('updates an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Old name']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collections/'.$catalog->id.'/items/'.$item->id, [
        'name' => 'New name',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'New name');

    expect($item->refresh()->name)->toBe('New name');
});

it('restricts item updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/collections/'.$catalog->id.'/items/'.$item->id, ['name' => 'New name'])
        ->assertNotFound();
});

it('deletes an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/collections/'.$catalog->id.'/items/'.$item->id)->assertNoContent();

    $this->assertSoftDeleted($item);
});

it('restricts item deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/collections/'.$catalog->id.'/items/'.$item->id)->assertNotFound();
});

// The API only edits the catalog fields. Renaming an item there must leave
// everything the web edit screen manages exactly where it was.
it('leaves the tags, custom field values and copies alone when updating an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $user->account;
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $catalog->catalogTypes()->attach($type);
    $field = CustomField::factory()->create(['type_id' => $type->id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);
    $set = Set::factory()->create(['catalog_id' => $catalog->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create([
        'catalog_id' => $catalog->id,
        'type_id' => $type->id,
        'category_id' => $category->id,
        'set_id' => $set->id,
        'name' => 'Old name',
    ]);
    $item->tags()->sync([$tag->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $field->id, 'value' => '300']);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collections/'.$catalog->id.'/items/'.$item->id, [
        'name' => 'New name',
        'type_id' => $type->id,
    ])->assertOk();

    $item->refresh();
    expect($item->name)->toBe('New name');
    expect($item->category_id)->toBe($category->id);
    expect($item->set_id)->toBe($set->id);
    expect($item->tags)->toHaveCount(1);
    expect($item->customFieldValues)->toHaveCount(1);
    expect($item->copies->pluck('id')->all())->toBe([$copy->id]);
});
