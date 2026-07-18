<?php

declare(strict_types=1);
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Condition;
use App\Models\CustomField;
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
