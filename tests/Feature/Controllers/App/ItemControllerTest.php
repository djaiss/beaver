<?php

declare(strict_types=1);
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\Models\CustomFieldValue;
use App\Models\Item;
use App\Models\Location;
use App\Models\Set;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('shows the add item form', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $this->actingAs($user)->get("/collections/{$collection->id}/items/new")
        ->assertOk()
        ->assertSee('Add an item')
        ->assertSee('Marvel Comics 1990s');
});

it('renders a star picker for a rating field on the add item form', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $collection->collectionTypes()->attach($type);
    CustomField::factory()->create(['type_id' => $type->id, 'name' => 'My Rating', 'field_type' => FieldTypeEnum::Rating]);

    $response = $this->actingAs($user)->get("/collections/{$collection->id}/items/new");

    $response->assertOk();
    $response->assertSee("x-if=\"field.type === 'rating'\"", false);
    $response->assertSee('x-for="star in 5"', false);
    // The plain input must not double up on a rating field.
    $response->assertSee("field.type !== 'rating'", false);
});

it('shows the item detail page', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create([
        'collection_id' => $collection->id,
        'category_id' => $category->id,
        'type_id' => $type->id,
        'name' => 'Amazing Spider-Man #1',
        'description' => 'The one where Chandler gets a chick and a duck.',
    ]);

    $response = $this->actingAs($user)->get(route('items.show', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('Amazing Spider-Man #1');
    $response->assertSee('Marvel Comics 1990s');
    $response->assertSee('Spider-Man');
    $response->assertSee('Comics');
    $response->assertSee('The one where Chandler gets a chick and a duck.');
    $response->assertSee('Overview');
    $response->assertSee('Copies');
    $response->assertSee('Roadmap');
});

it('shows the custom field values grouped the way the type orders them', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Publishing info']);
    $ungrouped = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Issue #', 'group_id' => null]);
    $grouped = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Publisher', 'group_id' => $group->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $ungrouped->id, 'value' => '300']);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $grouped->id, 'value' => 'Marvel']);

    $response = $this->actingAs($user)->get(route('items.show', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('Issue #');
    $response->assertSee('300');
    $response->assertSee('Publishing info');
    $response->assertSee('Publisher');
    $response->assertSee('Marvel');
});

it('reads a yes / no custom field as words rather than its stored one', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Signed', 'field_type' => FieldTypeEnum::Boolean, 'group_id' => null]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $field->id, 'value' => '1']);

    $response = $this->actingAs($user)->get(route('items.show', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('Signed');
    $response->assertSee('Yes');
});

it('reads a rating custom field as stars', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'My Rating', 'field_type' => FieldTypeEnum::Rating, 'group_id' => null]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $field->id, 'value' => '4']);

    $response = $this->actingAs($user)->get(route('items.show', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('My Rating');
    $response->assertSee('4 stars');
    // Four filled stars, then one empty one.
    $response->assertSee('<span class="text-hairline">★</span>', false);
    $response->assertDontSee('>4<', false);
});

it('shows the copies of an item with their condition and location', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $condition = Condition::factory()->create(['account_id' => $user->account_id, 'name' => 'Near Mint']);
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Display Case']);
    Copy::factory()->create([
        'item_id' => $item->id,
        'condition_id' => $condition->id,
        'location_id' => $location->id,
        'price_paid' => 18000,
        'estimated_value' => 42000,
    ]);

    $response = $this->actingAs($user)->get(route('items.show', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('Near Mint');
    $response->assertSee('Display Case');
    $response->assertSee('$420');
    $response->assertSee('$180');
});

it('flags the parts of the item screen that are not built yet', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'set_id' => $set->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->get(route('items.show', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('Soon');
    $response->assertSee('Provenance');
    $response->assertSee('Purchase &amp; sale history', false);
    $response->assertSee('Set completion needs a target size');
});

it('lets a viewer read an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man #1']);

    $this->actingAs($viewer)->get(route('items.show', [$collection, $item]))
        ->assertOk()
        ->assertSee('Amazing Spider-Man #1');
});

it('does not show an item belonging to another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.show', [$foreign, $item]))->assertNotFound();
});

it('does not show an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $other->id]);

    $this->actingAs($user)->get(route('items.show', [$collection, $item]))->assertNotFound();
});

it('does not show the form for another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->get("/collections/{$foreign->id}/items/new")
        ->assertNotFound();
});

it('does not let a viewer open the form', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get("/collections/{$collection->id}/items/new")
        ->assertNotFound();
});

it('creates an item with all its parts and redirects to the collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $user->account;
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($type);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Number]);
    $category = Category::factory()->create(['collection_id' => $collection->id]);
    $set = Set::factory()->create(['account_id' => $account->id]);
    $condition = Condition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $response = $this->actingAs($user)->post("/collections/{$collection->id}/items", [
        'name' => 'Amazing Spider-Man #1',
        'description' => 'The one Joey wants.',
        'type_id' => $type->id,
        'category_id' => $category->id,
        'set_id' => $set->id,
        'tag_ids' => [$tag->id],
        'new_tags' => ['First Issue'],
        'custom_fields' => [$field->id => '300'],
        'copies' => [
            ['condition_id' => $condition->id, 'location_id' => $location->id, 'acquired_at' => '2026-07-17', 'price_paid' => '42.00', 'estimated_value' => '99.00'],
        ],
    ]);

    $response->assertRedirect("/collections/{$collection->id}");

    $item = Item::query()->firstWhere('collection_id', $collection->id);
    expect($item->name)->toBe('Amazing Spider-Man #1');
    expect($item->category_id)->toBe($category->id);
    expect($item->set_id)->toBe($set->id);
    expect($item->tags)->toHaveCount(2);
    expect($item->customFieldValues->first()->value)->toBe('300');
    expect($item->copies)->toHaveCount(1);
    expect($item->copies->first()->price_paid)->toBe(4200);
    expect($item->copies->first()->estimated_value)->toBe(9900);
});

it('requires a name', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post("/collections/{$collection->id}/items", [
        'name' => '',
    ])->assertSessionHasErrors('name');
});

it('does not let a viewer create an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post("/collections/{$collection->id}/items", [
        'name' => 'Amazing Spider-Man #1',
    ])->assertNotFound();
});
