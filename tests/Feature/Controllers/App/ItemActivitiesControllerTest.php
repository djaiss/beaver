<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Enums\ItemActionEnum;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows the activity of an item, newest first', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $author = User::factory()->create(['first_name' => 'Rachel', 'last_name' => 'Green']);

    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $author->id,
        'action' => ItemActionEnum::ItemCreation->value,
        'created_at' => now()->subDay(),
    ]);
    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $author->id,
        'action' => ItemActionEnum::TagAttached->value,
        'parameters' => ['label' => 'Signed'],
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('item-tab-activities', false);
    $response->assertSee('Rachel Green');
    $response->assertSee('created this item');
    $response->assertSee('added the tag');
    $response->assertSeeInOrder(['added the tag', 'created this item'], false);
});

it('shows the change chips of an entry', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    ItemLog::factory()->create([
        'item_id' => $item->id,
        'action' => ItemActionEnum::CopyUpdate->value,
        'parameters' => ['changes' => [['label' => 'Estimated value', 'from' => '$390', 'to' => '$420']]],
    ]);

    $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]))
        ->assertOk()
        ->assertSee('Estimated value: $390 → $420');
});

it('tells the reader when an item has no activity yet', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]))
        ->assertOk()
        ->assertSee('No activity yet.');
});

it('does not show the activity of another item', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $other = Item::factory()->create(['catalog_id' => $catalog->id]);

    ItemLog::factory()->create([
        'item_id' => $other->id,
        'action' => ItemActionEnum::TagAttached->value,
        'parameters' => ['label' => 'Belongs elsewhere'],
    ]);

    $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]))
        ->assertOk()
        ->assertDontSee('Belongs elsewhere');
});

it('marks the activity tab as the current page', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]))
        ->assertOk()
        ->assertSee('data-test="item-tab-activities"', false)
        ->assertSee('aria-current="page"', false);
});

it('lets a viewer read the activity of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)->get(route('items.activities.index', [$catalog, $item]))->assertOk();
});

it('does not show the activity of an item belonging to another account', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();
    $item = Item::factory()->create(['catalog_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.activities.index', [$foreign, $item]))->assertNotFound();
});

it('does not show the activity of an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $other = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $other->id]);

    $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]))->assertNotFound();
});

it('shows the avatar of the author when they have one', function () {
    Storage::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $author = User::factory()->create([
        'account_id' => $user->account_id,
        'first_name' => 'Rachel',
        'last_name' => 'Green',
    ]);

    new UpdateUserAvatar(
        user: $author,
        file: UploadedFile::fake()->image('rachel.jpg', 400, 400),
    )->execute();

    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $author->id,
        'action' => ItemActionEnum::ItemCreation->value,
    ]);

    $response = $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee(route('profile.avatar.show', ['user' => $author, 'size' => 32]), escape: false);
});

it('falls back to the initials of the author when they have no avatar', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $author = User::factory()->create(['first_name' => 'Rachel', 'last_name' => 'Green']);

    ItemLog::factory()->create([
        'item_id' => $item->id,
        'user_id' => $author->id,
        'action' => ItemActionEnum::ItemCreation->value,
    ]);

    $response = $this->actingAs($user)->get(route('items.activities.index', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('RG');
});
