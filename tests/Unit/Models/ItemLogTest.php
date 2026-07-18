<?php

declare(strict_types=1);
use App\Enums\ItemActionEnum;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an item', function () {
    $itemLog = ItemLog::factory()->create();

    expect($itemLog->item()->exists())->toBeTrue();
    expect($itemLog->item)->toBeInstanceOf(Item::class);
});

it('belongs to a user', function () {
    $itemLog = ItemLog::factory()->create();

    expect($itemLog->user()->exists())->toBeTrue();
    expect($itemLog->user)->toBeInstanceOf(User::class);
});

it('gets the current name of the user who performed the action', function () {
    $user = User::factory()->create(['first_name' => 'Phoebe', 'last_name' => 'Buffay']);
    $itemLog = ItemLog::factory()->create(['user_id' => $user->id, 'user_name' => 'Phoebe Bouffet']);

    // The user still exists, so their name is read from them rather than from
    // the copy captured when the entry was written.
    expect($itemLog->getUserName())->toBe('Phoebe Buffay');
});

it('falls back to the captured name once the user is gone', function () {
    $itemLog = ItemLog::factory()->create(['user_id' => null, 'user_name' => 'Chandler Bing']);

    expect($itemLog->getUserName())->toBe('Chandler Bing');
});

it('translates the description of the action', function () {
    $itemLog = ItemLog::factory()->create(['action' => ItemActionEnum::TagAttached->value]);

    expect($itemLog->getTranslatedDescription())->toBe('added the tag');
});

it('falls back to the raw action when the enum does not know it', function () {
    $itemLog = ItemLog::factory()->create(['action' => 'smelly_cat']);

    expect($itemLog->getTranslatedDescription())->toBe('smelly_cat');
});

it('gets no chips when the entry carries no parameters', function () {
    $itemLog = ItemLog::factory()->create(['parameters' => null]);

    expect($itemLog->getChips())->toBe([]);
});

it('gets a plain chip from a label', function () {
    $itemLog = ItemLog::factory()->create(['parameters' => ['label' => 'Signed']]);

    expect($itemLog->getChips())->toBe([['style' => 'plain', 'label' => 'Signed']]);
});

it('gets a file chip from a filename', function () {
    $itemLog = ItemLog::factory()->create(['parameters' => ['file' => 'cgc-label.jpg']]);

    expect($itemLog->getChips())->toBe([['style' => 'file', 'label' => 'cgc-label.jpg']]);
});

it('gets one mono chip per value that moved', function () {
    $itemLog = ItemLog::factory()->create(['parameters' => ['changes' => [
        ['label' => 'Name', 'from' => 'Smelly Cat', 'to' => 'Smelly Dog'],
        ['label' => 'Price paid', 'from' => '$390', 'to' => '$420'],
    ]]]);

    expect($itemLog->getChips())->toBe([
        ['style' => 'mono', 'label' => 'Name: Smelly Cat → Smelly Dog'],
        ['style' => 'mono', 'label' => 'Price paid: $390 → $420'],
    ]);
});

it('reads a value that was not set before as empty', function () {
    $itemLog = ItemLog::factory()->create(['parameters' => ['changes' => [
        ['label' => 'Location', 'from' => null, 'to' => 'Box A1'],
    ]]]);

    expect($itemLog->getChips())->toBe([['style' => 'mono', 'label' => 'Location: empty → Box A1']]);
});

it('names a change on its own when it carries no values', function () {
    $itemLog = ItemLog::factory()->create(['parameters' => ['changes' => [
        ['label' => 'Description'],
    ]]]);

    expect($itemLog->getChips())->toBe([['style' => 'mono', 'label' => 'Description']]);
});
