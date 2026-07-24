<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\Category;
use App\Models\Copy;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use App\Models\CustomFieldValue;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\ItemPhoto;
use App\Models\Location;
use App\Models\Series;
use App\Models\Set;
use App\Models\Tag;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows the add item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertSee('Add an item')
        ->assertSee('Marvel Comics 1990s');
});

it('renders the type and copies help popovers on the add item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertSee('reveals that type\'s custom fields further down the form')
        ->assertSee('Add a second copy to this same item, never a second item');
});

it('renders the category, set, series and tags help popovers on the add item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertSee('an item belongs to at most one')
        ->assertSee('Filing an item into a set moves its owned count up against the set\'s target')
        ->assertSee('Ties this item to a franchise that can span multiple collections')
        ->assertSee('Pick an existing one or type a new one to create it on the spot');
});

it('offers the categories of the collection on the add item form, nested under their parent', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $parent = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider-Man']);
    $child = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man', 'parent_id' => $parent->id]);

    $response = $this->actingAs($user)->get("/collections/{$catalog->id}/items/new");

    $response->assertOk()
        // A child is listed straight after its parent, and indented rather than shown flat.
        ->assertSeeInOrder([
            '>Spider-Man</option>',
            '>'.str_repeat("\u{00A0}\u{00A0}\u{00A0}", 1).'Amazing Spider-Man</option>',
        ], false);
});

it('points at the categories screen when the collection has none yet', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertSee('This collection has no categories yet.')
        ->assertSee('/collections/'.$catalog->id.'/categories', false);
});

it('points at the sets screen when the collection has none yet', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertSee('This collection has no sets yet.')
        ->assertSee('/collections/'.$catalog->id.'/sets', false);
});

it('does not offer the sets of another collection on the add item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $other = Catalog::factory()->create(['account_id' => $user->account_id]);
    Set::factory()->create(['catalog_id' => $other->id, 'name' => 'Set from elsewhere']);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertDontSee('Set from elsewhere');
});

it('does not offer the categories of another collection on the add item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $other = Catalog::factory()->create(['account_id' => $user->account_id]);
    Category::factory()->create(['catalog_id' => $other->id, 'name' => 'Elsewhere']);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/new")
        ->assertOk()
        ->assertDontSee('Elsewhere');
});

it('shows the parent of a nested category on the item page', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $parent = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider-Man']);
    $child = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man', 'parent_id' => $parent->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'category_id' => $child->id]);

    $this->actingAs($user)->get("/collections/{$catalog->id}/items/{$item->id}")
        ->assertOk()
        ->assertSee('Spider-Man › Amazing Spider-Man');
});

it('renders a star picker for a rating field on the add item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $catalog->catalogTypes()->attach($type);
    CustomField::factory()->create(['type_id' => $type->id, 'name' => 'My Rating', 'field_type' => FieldTypeEnum::Rating]);

    $response = $this->actingAs($user)->get("/collections/{$catalog->id}/items/new");

    $response->assertOk();
    $response->assertSee("x-if=\"field.type === 'rating'\"", false);
    $response->assertSee('x-for="star in 5"', false);
    // The plain input must not double up on a rating field.
    $response->assertSee("field.type !== 'rating'", false);
});

it('shows the item detail page', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider-Man']);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create([
        'catalog_id' => $catalog->id,
        'category_id' => $category->id,
        'type_id' => $type->id,
        'name' => 'Amazing Spider-Man #1',
        'description' => 'The one where Chandler gets a chick and a duck.',
    ]);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

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
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Publishing info']);
    $ungrouped = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Issue #', 'group_id' => null]);
    $grouped = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Publisher', 'group_id' => $group->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $ungrouped->id, 'value' => '300']);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $grouped->id, 'value' => 'Marvel']);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('Issue #');
    $response->assertSee('300');
    $response->assertSee('Publishing info');
    $response->assertSee('Publisher');
    $response->assertSee('Marvel');
});

it('reads a yes / no custom field as words rather than its stored one', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Signed', 'field_type' => FieldTypeEnum::Boolean, 'group_id' => null]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $field->id, 'value' => '1']);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('Signed');
    $response->assertSee('Yes');
});

it('reads a rating custom field as stars', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'My Rating', 'field_type' => FieldTypeEnum::Rating, 'group_id' => null]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $field->id, 'value' => '4']);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('My Rating');
    $response->assertSee('4 stars');
    // Four filled stars, then one empty one.
    $response->assertSee('<span class="text-hairline">★</span>', false);
    $response->assertDontSee('>4<', false);
});

it('shows how complete the set of an item is', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1-10', 'target_count' => 10]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'set_id' => $set->id]);
    Item::factory()->count(3)->create(['catalog_id' => $catalog->id, 'set_id' => $set->id]);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk()
        ->assertSee('Part of a set')
        ->assertSee('Amazing Spider-Man #1-10')
        ->assertSee('4/10')
        ->assertSee('style="width: 40%"', false)
        ->assertSee(route('sets.index', $catalog->id), false);
});

it('falls back to an item count when the set has no target', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['catalog_id' => $catalog->id, 'target_count' => null]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'set_id' => $set->id]);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk()
        ->assertSee('1 item in this set')
        ->assertDontSee('data-test="set-completion-ratio"', false);
});

it('lets a viewer read an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #1']);

    $this->actingAs($viewer)->get(route('items.show', [$catalog, $item]))
        ->assertOk()
        ->assertSee('Amazing Spider-Man #1');
});

it('does not show an item belonging to another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();
    $item = Item::factory()->create(['catalog_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.show', [$foreign, $item]))->assertNotFound();
});

it('does not show an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $other = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $other->id]);

    $this->actingAs($user)->get(route('items.show', [$catalog, $item]))->assertNotFound();
});

it('does not show the form for another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();

    $this->actingAs($user)->get("/collections/{$foreign->id}/items/new")
        ->assertNotFound();
});

it('does not let a viewer open the form', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get("/collections/{$catalog->id}/items/new")
        ->assertNotFound();
});

it('creates an item with all its parts and redirects to the collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $user->account;
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $catalog->catalogTypes()->attach($type);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Number]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);
    $set = Set::factory()->create(['catalog_id' => $catalog->id]);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $response = $this->actingAs($user)->post("/collections/{$catalog->id}/items", [
        'name' => 'Amazing Spider-Man #1',
        'description' => 'The one Joey wants.',
        'type_id' => $type->id,
        'category_id' => $category->id,
        'set_id' => $set->id,
        'tag_ids' => [$tag->id],
        'new_tags' => ['First Issue'],
        'custom_fields' => [$field->id => '300'],
        'copies' => [
            [
                'identifier' => 'CGC 1234567',
                'item_condition_id' => $condition->id,
                'current_location_id' => $location->id,
                'status' => CopyStatus::Loaned->value,
                'quantity' => '2',
                'note' => 'Lent to Joey.',
                'estimated_value' => '99.00',
            ],
        ],
    ]);

    $response->assertRedirect("/collections/{$catalog->id}");

    $item = Item::query()->firstWhere('catalog_id', $catalog->id);
    expect($item->name)->toBe('Amazing Spider-Man #1');
    expect($item->category_id)->toBe($category->id);
    expect($item->set_id)->toBe($set->id);
    expect($item->tags)->toHaveCount(2);
    expect($item->customFieldValues->first()->value)->toBe('300');
    expect($item->copies)->toHaveCount(1);
    expect($item->copies->first()->identifier)->toBe('CGC 1234567');
    expect($item->copies->first()->current_location_id)->toBe($location->id);
    expect($item->copies->first()->status)->toBe(CopyStatus::Loaned);
    expect($item->copies->first()->quantity)->toBe(2);
    expect($item->copies->first()->note)->toBe('Lent to Joey.');
    // The estimated value is a valuation now, not a column on the copy.
    expect($item->copies->first()->estimatedValue())->toBe(9900);
});

it('requires a name', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post("/collections/{$catalog->id}/items", [
        'name' => '',
    ])->assertSessionHasErrors('name');
});

it('does not let a viewer create an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post("/collections/{$catalog->id}/items", [
        'name' => 'Amazing Spider-Man #1',
    ])->assertNotFound();
});

it('shows the edit item form filled in with the current values', function () {
    $user = $this->createUser();
    $account = $user->account;
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $type = CatalogType::factory()->create(['account_id' => $account->id, 'name' => 'Comics']);
    $catalog->catalogTypes()->attach($type);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Silver age']);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);
    $item = Item::factory()->create([
        'catalog_id' => $catalog->id,
        'type_id' => $type->id,
        'category_id' => $category->id,
        'name' => 'Fantastic Four #1',
    ]);
    $item->tags()->sync([$tag->id]);

    $response = $this->actingAs($user)->get(route('items.edit', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('Edit item');
    $response->assertSee('Fantastic Four #1');
    $response->assertSee('Silver age');
    $response->assertSee('Signed');
    $response->assertSee('Comics');
});

it('renders the type and copies help popovers on the edit item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $response = $this->actingAs($user)->get(route('items.edit', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('reveals that type\'s custom fields further down the form');
    $response->assertSee('Add a second copy to this same item, never a second item');
});

it('renders the category, set, series and tags help popovers on the edit item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $response = $this->actingAs($user)->get(route('items.edit', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('an item belongs to at most one');
    $response->assertSee('Filing an item into a set moves its owned count up against the set\'s target');
    $response->assertSee('Ties this item to a franchise that can span multiple collections');
    $response->assertSee('Pick an existing one or type a new one to create it on the spot');
});

// The estimated value lives in the valuations now, so the form has to read the
// latest one back rather than a column on the copy.
it('fills the copy rows of the edit form with the current values', function () {
    $user = $this->createUser();
    $account = $user->account;
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'identifier' => 'CGC 1234567',
        'status' => CopyStatus::Loaned,
        'quantity' => 2,
        'note' => 'Lent to Joey.',
    ]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 42000]);

    $response = $this->actingAs($user)->get(route('items.edit', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('CGC 1234567');
    $response->assertSee('Lent to Joey.');
    $response->assertSee('420.00');
    $response->assertSee('Loaned out');
});

it('offers the edit link on the item page', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->get(route('items.show', [$catalog, $item]))
        ->assertOk()
        ->assertSee("/collections/{$catalog->id}/items/{$item->id}/edit");
});

it('does not offer the edit link to a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)->get(route('items.show', [$catalog, $item]))
        ->assertOk()
        ->assertDontSee("/collections/{$catalog->id}/items/{$item->id}/edit");
});

it('does not let a viewer open the edit form', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)->get(route('items.edit', [$catalog, $item]))->assertNotFound();
});

it('does not show the edit form for another accounts item', function () {
    $user = $this->createUser();
    $foreignCatalog = Catalog::factory()->create();
    $foreignItem = Item::factory()->create(['catalog_id' => $foreignCatalog->id]);

    $this->actingAs($user)->get("/collections/{$foreignCatalog->id}/items/{$foreignItem->id}/edit")
        ->assertNotFound();
});

it('updates an item with all its parts and redirects to the item', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $user->account;
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $catalog->catalogTypes()->attach($type);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Number]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);
    $set = Set::factory()->create(['catalog_id' => $catalog->id]);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Fantastic Four #1']);
    $keptCopy = Copy::factory()->create(['item_id' => $item->id]);
    $droppedCopy = Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->put(route('items.update', [$catalog, $item]), [
        'name' => 'Amazing Spider-Man #1',
        'description' => 'The one Joey wants.',
        'type_id' => $type->id,
        'category_id' => $category->id,
        'set_id' => $set->id,
        'tag_ids' => [$tag->id],
        'new_tags' => ['First Issue'],
        'custom_fields' => [$field->id => '300'],
        'copies' => [
            [
                'id' => $keptCopy->id,
                'identifier' => 'CGC 1234567',
                'item_condition_id' => $condition->id,
                'current_location_id' => $location->id,
                'status' => CopyStatus::Sold->value,
                'quantity' => '2',
                'disposed_at' => '2026-07-17',
                'estimated_value' => '99.00',
            ],
        ],
    ]);

    $response->assertRedirect("/collections/{$catalog->id}/items/{$item->id}");
    $response->assertSessionHas('status', 'Item updated');

    $item->refresh();
    expect($item->name)->toBe('Amazing Spider-Man #1');
    expect($item->description)->toBe('The one Joey wants.');
    expect($item->type_id)->toBe($type->id);
    expect($item->category_id)->toBe($category->id);
    expect($item->set_id)->toBe($set->id);
    expect($item->tags)->toHaveCount(2);
    expect($item->customFieldValues->first()->value)->toBe('300');
    expect($item->copies)->toHaveCount(1);
    expect($item->copies->first()->id)->toBe($keptCopy->id);
    expect($item->copies->first()->identifier)->toBe('CGC 1234567');
    expect($item->copies->first()->status)->toBe(CopyStatus::Sold);
    expect($item->copies->first()->quantity)->toBe(2);
    expect($item->copies->first()->disposed_at->toDateString())->toBe('2026-07-17');
    expect($item->copies->first()->estimatedValue())->toBe(9900);
    $this->assertSoftDeleted('copies', ['id' => $droppedCopy->id]);
});

it('requires a name when updating', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->put(route('items.update', [$catalog, $item]), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('does not let a viewer update an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)->put(route('items.update', [$catalog, $item]), [
        'name' => 'Amazing Spider-Man #1',
    ])->assertNotFound();
});

it('deletes an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $response = $this->actingAs($user)->delete(route('items.destroy', [$catalog, $item]));

    $response->assertRedirect("/collections/{$catalog->id}");
    $response->assertSessionHas('status', 'Item deleted');
    $this->assertSoftDeleted('items', ['id' => $item->id]);
});

it('does not let a viewer delete an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)->delete(route('items.destroy', [$catalog, $item]))->assertNotFound();
    $this->assertDatabaseHas('items', ['id' => $item->id, 'deleted_at' => null]);
});

it('does not delete an item in another account', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();
    $item = Item::factory()->create(['catalog_id' => $foreign->id]);

    $this->actingAs($user)->delete(route('items.destroy', [$foreign, $item]))->assertNotFound();
});

// An unchecked box submits nothing on its own, so the form carries an empty
// value alongside it. Without that, a yes could never be turned back into a no.
it('clears a yes / no custom field when it is unchecked', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $user->account;
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $catalog->catalogTypes()->attach($type);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Boolean]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'type_id' => $type->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $field->id, 'value' => '1']);

    $this->actingAs($user)->put(route('items.update', [$catalog, $item]), [
        'name' => 'Amazing Spider-Man #1',
        'type_id' => $type->id,
        'custom_fields' => [$field->id => ''],
    ])->assertRedirect();

    expect($item->refresh()->customFieldValues)->toHaveCount(0);
});

// A browser sends every id as a string. The actions match ids with a strict
// comparison, so the controller has to hand them over as integers.
it('accepts a condition and a location sent as strings when creating', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $condition = ItemCondition::factory()->create(['account_id' => $user->account_id]);
    $location = Location::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post("/collections/{$catalog->id}/items", [
        'name' => 'Amazing Spider-Man #1',
        'copies' => [
            ['item_condition_id' => (string) $condition->id, 'current_location_id' => (string) $location->id],
        ],
    ])->assertRedirect("/collections/{$catalog->id}");

    $item = Item::query()->firstWhere('catalog_id', $catalog->id);
    expect($item->copies->first()->item_condition_id)->toBe($condition->id);
    expect($item->copies->first()->current_location_id)->toBe($location->id);
});

it('accepts copy ids, conditions and locations sent as strings when updating', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $condition = ItemCondition::factory()->create(['account_id' => $user->account_id]);
    $location = Location::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->put(route('items.update', [$catalog, $item]), [
        'name' => 'Amazing Spider-Man #1',
        'copies' => [
            ['id' => (string) $copy->id, 'item_condition_id' => (string) $condition->id, 'current_location_id' => (string) $location->id],
        ],
    ])->assertRedirect("/collections/{$catalog->id}/items/{$item->id}");

    $copy->refresh();
    expect($copy->item_condition_id)->toBe($condition->id);
    expect($copy->current_location_id)->toBe($location->id);
    expect($item->refresh()->copies)->toHaveCount(1);
});

// The tab row scrolls sideways on a narrow screen, and a browser turns that into
// a vertical scrollbar unless overflow-y is pinned, because the active tab's
// underline hangs a pixel over the container's border.
it('does not let the item tabs scroll vertically', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->get(route('items.show', [$catalog, $item]))
        ->assertOk()
        ->assertSee('overflow-x-auto overflow-y-hidden', false);
});

it('shows the tags of an item with the controls to change them', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $onItem = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);
    Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Key issue']);
    $item->tags()->sync([$onItem->id]);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('Signed');
    $response->assertSee('add-tag-input', false);
    $response->assertSee('remove-tag-'.$onItem->id, false);
    // The account's other tags are offered as suggestions.
    $response->assertSee('Key issue');
});

it('shows a viewer the tags of an item without the controls to change them', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);
    $item->tags()->sync([$tag->id]);

    $response = $this->actingAs($viewer)->get(route('items.show', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('Signed');
    $response->assertDontSee('add-tag-input', false);
    $response->assertDontSee('remove-tag-'.$tag->id, false);
});

it('uploads several photos when creating an item', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post("/collections/{$catalog->id}/items", [
        'name' => 'Amazing Spider-Man #1',
        'photos' => [
            UploadedFile::fake()->image('front.jpg'),
            UploadedFile::fake()->image('back.jpg'),
        ],
    ])->assertRedirect(route('collections.show', $catalog));

    $item = Item::query()->first();

    expect($item->photos()->count())->toBe(2);
    expect($item->mainPhoto()->first()->filename)->toBe('front.jpg');
});

it('rejects a photo that is not an image', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post("/collections/{$catalog->id}/items", [
        'name' => 'Amazing Spider-Man #1',
        'photos' => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')],
    ])->assertSessionHasErrors('photos.0');
});

it('shows the photos an item already has on the edit form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $cover = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true, 'position' => 1]);
    $other = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => false, 'position' => 2]);

    $response = $this->actingAs($user)->get(route('items.edit', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('item-photos', false);
    $response->assertSee('add-photos', false);
    // Both photos reach the form, with the cover already chosen. Blade hands
    // the array to Alpine through JSON.parse, so the quotes arrive escaped.
    $response->assertSee('\u0022id\u0022:'.$cover->id, false);
    $response->assertSee('\u0022id\u0022:'.$other->id, false);
    $response->assertSee('mainPhotoId: '.$cover->id, false);
});

it('adds, removes and re-covers photos when updating an item', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $dropped = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true, 'position' => 1]);
    $promoted = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => false, 'position' => 2]);

    $this->actingAs($user)->put("/collections/{$catalog->id}/items/{$item->id}", [
        'name' => 'Amazing Spider-Man #1',
        'photos' => [UploadedFile::fake()->image('spine.jpg')],
        'deleted_photos' => [$dropped->id],
        'main_photo_id' => $promoted->id,
    ])->assertRedirect(route('items.show', [$catalog, $item]));

    $this->assertModelMissing($dropped);
    expect($item->photos()->count())->toBe(2);
    expect($item->mainPhoto()->first()->id)->toBe($promoted->id);
});

it('leaves the photos alone when the edit form sends none', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true]);

    $this->actingAs($user)->put("/collections/{$catalog->id}/items/{$item->id}", [
        'name' => 'Amazing Spider-Man #1',
    ])->assertRedirect(route('items.show', [$catalog, $item]));

    expect($item->photos()->count())->toBe(1);
    expect($item->mainPhoto()->first()->id)->toBe($photo->id);
});

it('counts which photo is on screen when an item has several', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    ItemPhoto::factory()->count(3)->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->get(route('items.show', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('photo-position', false);
    $response->assertSee('x-text="photo + 1"', false);
    $response->assertSee('/ 3');
    // The counter sits inside the photo, in its bottom right corner.
    $response->assertSee('aspect-4/3 w-full overflow-hidden', false);
    $response->assertSee('right-3.5', false);
    $response->assertSee('bottom-3.5', false);
});

it('does not count the photos of an item that has only one', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    ItemPhoto::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.show', [$catalog, $item]))
        ->assertOk()
        ->assertDontSee('photo-position', false);
});

it('does not count the photos of an item that has none', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->get(route('items.show', [$catalog, $item]))
        ->assertOk()
        ->assertDontSee('photo-position', false);
});

it('shows the series of an item on the overview, with its reach', function () {
    $user = $this->createUser();
    $series = Series::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Harry Potter',
        'description' => 'The wizarding world.',
    ]);

    $books = Catalog::factory()->create(['account_id' => $user->account_id]);
    $lego = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $books->id, 'series_id' => $series->id]);
    Item::factory()->create(['catalog_id' => $lego->id, 'series_id' => $series->id]);

    $response = $this->actingAs($user)->get('/collections/'.$books->id.'/items/'.$item->id);

    $response->assertOk()
        ->assertSee('Harry Potter')
        ->assertSee('The wizarding world.')
        ->assertSee('Account-wide')
        ->assertSee('2 items across 2 collections')
        ->assertSee('View series');
});

it('does not show the series card on an item without a series', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'series_id' => null]);

    $this->actingAs($user)->get('/collections/'.$catalog->id.'/items/'.$item->id)
        ->assertOk()
        ->assertDontSee('Account-wide');
});

it('offers the series of the account on the item form', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Star Wars']);
    Series::factory()->create(['name' => 'Another account’s series']);

    $this->actingAs($user)->get('/collections/'.$catalog->id.'/items/new')
        ->assertOk()
        ->assertSee('Star Wars')
        ->assertDontSee('Another account’s series');
});
