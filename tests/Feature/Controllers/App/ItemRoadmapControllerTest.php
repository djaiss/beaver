<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows what an item will eventually track', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $response = $this->actingAs($user)->get(route('items.roadmap.index', [$catalog, $item]));

    $response->assertOk();
    $response->assertSee('Purchase &amp; sale history', false);
    $response->assertSee('Soon');
});

it('marks the roadmap tab as the current page', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)->get(route('items.roadmap.index', [$catalog, $item]))
        ->assertOk()
        ->assertSee('data-test="item-tab-roadmap"', false)
        ->assertSee('aria-current="page"', false);
});

it('lets a viewer read the roadmap of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)->get(route('items.roadmap.index', [$catalog, $item]))->assertOk();
});

it('does not show the roadmap of an item belonging to another account', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();
    $item = Item::factory()->create(['catalog_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.roadmap.index', [$foreign, $item]))->assertNotFound();
});

it('does not show the roadmap of an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $other = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $other->id]);

    $this->actingAs($user)->get(route('items.roadmap.index', [$catalog, $item]))->assertNotFound();
});
