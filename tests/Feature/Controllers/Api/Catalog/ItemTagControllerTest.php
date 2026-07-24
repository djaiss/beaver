<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create an item belonging to the given account, for the item tag tests.
 */
function itemTagTestItem(int $accountId): Item
{
    $catalog = Catalog::factory()->create(['account_id' => $accountId]);

    return Item::factory()->create(['catalog_id' => $catalog->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the tags of an item', function () {
    $user = $this->createUser();
    $item = itemTagTestItem($user->account_id);
    $signed = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);
    $firstIssue = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'First Issue']);
    $item->tags()->attach([$signed->id, $firstIssue->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/tags')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
        ])
        ->assertJsonPath('data.0.type', 'tag')
        ->assertJsonPath('data.0.attributes.name', 'Signed')
        ->assertJsonPath('data.1.attributes.name', 'First Issue');
});

it('does not list the tags of an item from another account', function () {
    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $item = itemTagTestItem($otherAccount->id);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/tags')
        ->assertNotFound();
});

it('attaches a tag to an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemTagTestItem($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/tags', [
        'name' => 'Signed',
    ])
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'tag')
        ->assertJsonPath('data.attributes.name', 'Signed');

    expect($item->tags()->count())->toBe(1);
    expect($item->tags()->first()->name)->toBe('Signed');
});

it('reuses a tag the account already knows when attaching it to an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemTagTestItem($user->account_id);
    $tag = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/tags', [
        'name' => 'Signed',
    ])
        ->assertCreated()
        ->assertJsonPath('data.id', (string) $tag->id);

    expect(Tag::query()->count())->toBe(1);
});

it('validates the name when attaching a tag to an item', function () {
    $user = $this->createUser();
    $item = itemTagTestItem($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/tags', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('returns not found when attaching a tag to an item of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $item = itemTagTestItem($otherAccount->id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/tags', [
        'name' => 'Signed',
    ])
        ->assertNotFound();
});

it('restricts attaching a tag to an item to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = itemTagTestItem($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/items/'.$item->id.'/tags', [
        'name' => 'Signed',
    ])
        ->assertNotFound();
});

it('detaches a tag from an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemTagTestItem($user->account_id);
    $tag = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);
    $item->tags()->attach($tag->id);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/items/'.$item->id.'/tags/'.$tag->id)
        ->assertNoContent();

    expect($item->tags()->count())->toBe(0);
    $this->assertModelExists($tag);
});

it('returns not found when detaching a tag from another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = itemTagTestItem($user->account_id);
    $tag = Tag::factory()->create();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/items/'.$item->id.'/tags/'.$tag->id)
        ->assertNotFound();
});

it('restricts detaching a tag from an item to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = itemTagTestItem($account->id);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);
    $item->tags()->attach($tag->id);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/items/'.$item->id.'/tags/'.$tag->id)
        ->assertNotFound();
});
