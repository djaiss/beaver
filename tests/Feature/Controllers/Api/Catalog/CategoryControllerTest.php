<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Category;
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
            'parent_id',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the categories of a collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Marvel']);
    Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'DC']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/categories')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list categories from another account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create();
    Category::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/categories')->assertNotFound();
});

it('shows a category', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Marvel']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/categories/'.$category->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'category')
        ->assertJsonPath('data.attributes.name', 'Marvel')
        ->assertJsonPath('data.links.self', route('api.collections.categories.show', [$catalog->id, $category->id]));
});

it('returns not found for a category from another collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$catalog->id.'/categories/'.$category->id)->assertNotFound();
});

it('creates a category', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collections/'.$catalog->id.'/categories', [
        'name' => 'Marvel',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'Marvel')
        ->assertJsonPath('data.attributes.catalog_id', (string) $catalog->id);

    $category = Category::query()->latest('id')->first();
    expect($category->catalog_id)->toBe($catalog->id);
});

it('creates a nested category', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $parent = Category::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collections/'.$catalog->id.'/categories', [
        'name' => 'Spider-Man',
        'parent_id' => $parent->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.parent_id', (string) $parent->id);
});

it('validates the name when creating a category', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collections/'.$catalog->id.'/categories', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts category creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/collections/'.$catalog->id.'/categories', ['name' => 'Marvel'])
        ->assertNotFound();
});

it('updates a category', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Old name']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collections/'.$catalog->id.'/categories/'.$category->id, [
        'name' => 'Marvel',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Marvel');

    expect($category->refresh()->name)->toBe('Marvel');
});

it('restricts category updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/collections/'.$catalog->id.'/categories/'.$category->id, ['name' => 'Marvel'])
        ->assertNotFound();
});

it('deletes a category', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/collections/'.$catalog->id.'/categories/'.$category->id)->assertNoContent();

    $this->assertSoftDeleted($category);
});

it('restricts category deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/collections/'.$catalog->id.'/categories/'.$category->id)->assertNotFound();
});
