<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\VisibilityEnum;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

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
