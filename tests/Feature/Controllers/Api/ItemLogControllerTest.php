<?php

declare(strict_types=1);
use App\Enums\ItemActionEnum;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create an item belonging to the given account.
 */
function itemOfAccount(int $accountId): Item
{
    $collection = Collection::factory()->create(['account_id' => $accountId]);

    return Item::factory()->create(['collection_id' => $collection->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'user_name',
            'action',
            'parameters',
            'description',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the activity of an item, newest first', function () {
    $user = $this->createUser();
    $item = itemOfAccount($user->account_id);

    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $user->id,
        'action' => ItemActionEnum::ItemCreation->value,
        'created_at' => now()->subDay(),
    ]);
    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $user->id,
        'action' => ItemActionEnum::TagAttached->value,
        'parameters' => ['label' => 'Signed'],
        'created_at' => now(),
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/items/'.$item->id.'/logs');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonStructure(['data' => [$this->jsonStructure]]);
    $response->assertJsonPath('data.0.type', 'item_log');
    $response->assertJsonPath('data.0.attributes.action', 'tag_attached');
    $response->assertJsonPath('data.0.attributes.description', 'added the tag');
    $response->assertJsonPath('data.0.attributes.parameters.label', 'Signed');
    $response->assertJsonPath('data.1.attributes.action', 'item_created');
});

it('names the user who performed the action', function () {
    $user = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $item = itemOfAccount($user->account_id);
    ItemLog::factory()->create(['item_id' => $item->id, 'user_id' => $user->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/logs')
        ->assertOk()
        ->assertJsonPath('data.0.attributes.user_name', 'Rachel Green');
});

it('falls back to the captured name once the user is gone', function () {
    $user = $this->createUser();
    $item = itemOfAccount($user->account_id);
    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => null,
        'user_name' => 'Chandler Bing',
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/logs')
        ->assertOk()
        ->assertJsonPath('data.0.attributes.user_name', 'Chandler Bing');
});

it('does not list the activity of an item from another account', function () {
    $user = $this->createUser();
    $foreign = itemOfAccount(User::factory()->create()->account_id);
    ItemLog::factory()->create(['item_id' => $foreign->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$foreign->id.'/logs')->assertNotFound();
});

it('lets a viewer read the activity of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $item = itemOfAccount($account->id);
    ItemLog::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($viewer);

    $this->json('GET', '/api/items/'.$item->id.'/logs')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('shows one entry of the activity of an item', function () {
    $user = $this->createUser();
    $item = itemOfAccount($user->account_id);
    $log = ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $user->id,
        'action' => ItemActionEnum::CopyUpdate->value,
        'parameters' => ['changes' => [['label' => 'Price paid', 'from' => '$390', 'to' => '$420']]],
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/items/'.$item->id.'/logs/'.$log->id);

    $response->assertOk();
    $response->assertJsonStructure(['data' => $this->jsonStructure]);
    $response->assertJsonPath('data.id', (string) $log->id);
    $response->assertJsonPath('data.attributes.action', 'copy_updated');
    $response->assertJsonPath('data.attributes.parameters.changes.0.to', '$420');
});

it('returns not found for an entry belonging to another item', function () {
    $user = $this->createUser();
    $item = itemOfAccount($user->account_id);
    $other = itemOfAccount($user->account_id);
    $log = ItemLog::factory()->create(['item_id' => $other->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/logs/'.$log->id)->assertNotFound();
});

it('returns not found for an entry from another account', function () {
    $user = $this->createUser();
    $foreign = itemOfAccount(User::factory()->create()->account_id);
    $log = ItemLog::factory()->create(['item_id' => $foreign->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$foreign->id.'/logs/'.$log->id)->assertNotFound();
});

it('paginates the activity of an item', function () {
    $user = $this->createUser();
    $item = itemOfAccount($user->account_id);
    ItemLog::factory()->count(3)->create(['item_id' => $item->id, 'user_id' => $user->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/logs?per_page=2')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});
