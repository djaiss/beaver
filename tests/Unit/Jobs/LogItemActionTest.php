<?php

declare(strict_types=1);
use App\Enums\ItemActionEnum;
use App\Jobs\LogItemAction;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs an action performed on an item', function () {
    $user = User::factory()->create(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $item = Item::factory()->create();

    LogItemAction::dispatch(
        item: $item,
        user: $user,
        action: ItemActionEnum::TagAttached,
        parameters: ['label' => 'Signed'],
    );

    $itemLog = ItemLog::query()->first();

    expect($itemLog->item_id)->toBe($item->id);
    expect($itemLog->getUserName())->toEqual('Monica Geller');
    expect($itemLog->action)->toEqual('tag_attached');
    expect($itemLog->getTranslatedDescription())->toEqual('added the tag');
    expect($itemLog->getChips())->toBe([['style' => 'plain', 'label' => 'Signed']]);
});

it('logs an action that carries no parameters', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create();

    LogItemAction::dispatch(
        item: $item,
        user: $user,
        action: ItemActionEnum::ItemCreation,
    );

    $itemLog = ItemLog::query()->first();

    expect($itemLog->parameters)->toBeNull();
    expect($itemLog->getChips())->toBe([]);
});

it('does not stamp the last activity of the user', function () {
    $user = User::factory()->create(['last_activity_at' => null]);
    $item = Item::factory()->create();

    LogItemAction::dispatch(
        item: $item,
        user: $user,
        action: ItemActionEnum::ItemCreation,
    );

    // That stamp belongs to LogUserAction, which runs alongside this job.
    expect($user->refresh()->last_activity_at)->toBeNull();
});
