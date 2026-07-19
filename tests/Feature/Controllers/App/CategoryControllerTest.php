<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the categories of a collection, nested', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $spiderMan = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man', 'parent_id' => $spiderMan->id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/categories');

    $response->assertOk()
        ->assertSee('Spider-Man')
        ->assertSee('Amazing Spider-Man')
        ->assertSee('1 subcategory');
});

it('counts the items filed under a category', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    Item::factory()->count(3)->create(['collection_id' => $collection->id, 'category_id' => $category->id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/categories');

    $response->assertOk()
        ->assertSee('data-test="category-items-'.$category->id.'"', false)
        ->assertSeeInOrder(['1 top-level', '1 category total']);
});

it('shows a breadcrumb back to the collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/categories');

    $response->assertOk()
        ->assertSeeInOrder(['Collections', 'Marvel Comics 1990s', 'Categories'])
        ->assertSee(route('collections.index'), false)
        ->assertSee(route('collections.show', $collection->id), false);
});

it('shows the empty state when the collection has no categories', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/categories');

    $response->assertOk()
        ->assertSee('No categories yet')
        ->assertSee('Example structure');
});

it('does not list the categories of another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    Category::factory()->create(['collection_id' => $foreign->id, 'name' => 'Foreign Category']);

    $this->actingAs($user)->get('/collections/'.$foreign->id.'/categories')->assertNotFound();
});

it('allows a viewer to list categories', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);

    $this->actingAs($viewer)->get('/collections/'.$collection->id.'/categories')
        ->assertOk()
        ->assertSee('Spider-Man');
});

it('creates a category', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->post('/collections/'.$collection->id.'/categories', [
        'name' => 'Spider-Man',
    ]);

    $response->assertRedirect('/collections/'.$collection->id.'/categories');
    $response->assertSessionHas('status', 'Category created');

    $category = Category::query()->first();
    expect($category)->not->toBeNull();
    expect($category->name)->toBe('Spider-Man');
    expect($category->collection_id)->toBe($collection->id);
    expect($category->parent_id)->toBeNull();
});

it('creates a nested category from a form-encoded parent id', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $parent = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);

    $response = $this->actingAs($user)->post('/collections/'.$collection->id.'/categories', [
        'name' => 'Amazing Spider-Man',
        'parent_id' => (string) $parent->id,
    ]);

    $response->assertRedirect('/collections/'.$collection->id.'/categories');

    $child = Category::query()->where('parent_id', $parent->id)->first();
    expect($child)->not->toBeNull();
    expect($child->name)->toBe('Amazing Spider-Man');
});

it('refuses a name that is missing', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post('/collections/'.$collection->id.'/categories', ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('refuses to create a category in another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->post('/collections/'.$foreign->id.'/categories', ['name' => 'Spider-Man'])
        ->assertNotFound();

    expect(Category::query()->count())->toBe(0);
});

it('refuses to let a viewer create a category', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post('/collections/'.$collection->id.'/categories', ['name' => 'Spider-Man'])
        ->assertNotFound();

    expect(Category::query()->count())->toBe(0);
});

it('updates a category', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);

    $response = $this->actingAs($user)->put('/collections/'.$collection->id.'/categories/'.$category->id, [
        'name' => 'Spidey',
    ]);

    $response->assertRedirect('/collections/'.$collection->id.'/categories');
    $response->assertSessionHas('status', 'Category updated');

    expect($category->fresh()->name)->toBe('Spidey');
});

it('refuses to update a category of another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $category = Category::factory()->create(['collection_id' => $foreign->id, 'name' => 'Foreign Category']);

    $this->actingAs($user)->put('/collections/'.$foreign->id.'/categories/'.$category->id, ['name' => 'Mine now'])
        ->assertNotFound();

    expect($category->fresh()->name)->toBe('Foreign Category');
});

it('refuses to update a category that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['collection_id' => $other->id, 'name' => 'Elsewhere']);

    $this->actingAs($user)->put('/collections/'.$collection->id.'/categories/'.$category->id, ['name' => 'Moved'])
        ->assertNotFound();

    expect($category->fresh()->name)->toBe('Elsewhere');
});

it('deletes a category and its subcategories', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $parent = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    $child = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man', 'parent_id' => $parent->id]);

    $response = $this->actingAs($user)->delete('/collections/'.$collection->id.'/categories/'.$parent->id);

    $response->assertRedirect('/collections/'.$collection->id.'/categories');
    $response->assertSessionHas('status', 'Category deleted');

    $this->assertSoftDeleted($parent);
    $this->assertSoftDeleted($child);
});

it('keeps the items when their category is deleted', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'category_id' => $category->id]);

    $this->actingAs($user)->delete('/collections/'.$collection->id.'/categories/'.$category->id)
        ->assertRedirect('/collections/'.$collection->id.'/categories');

    $this->assertModelExists($item);
});

it('refuses to let a viewer delete a category', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);

    $this->actingAs($viewer)->delete('/collections/'.$collection->id.'/categories/'.$category->id)
        ->assertNotFound();

    $this->assertModelExists($category);
});
