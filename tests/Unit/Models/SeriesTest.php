<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = Account::factory()->create();
    $series = Series::factory()->create(['account_id' => $account->id]);

    expect($series->account)->toBeInstanceOf(Account::class);
    expect($series->account->id)->toBe($account->id);
    expect($account->series->pluck('id'))->toContain($series->id);
});

it('has many items', function () {
    $series = Series::factory()->create();
    $item = Item::factory()->create(['series_id' => $series->id]);

    expect($series->items->pluck('id'))->toContain($item->id);
});

it('gathers items from several collections of the account', function () {
    $account = Account::factory()->create();
    $series = Series::factory()->create(['account_id' => $account->id]);

    $books = Collection::factory()->create(['account_id' => $account->id]);
    $films = Collection::factory()->create(['account_id' => $account->id]);

    $book = Item::factory()->create(['collection_id' => $books->id, 'series_id' => $series->id]);
    $film = Item::factory()->create(['collection_id' => $films->id, 'series_id' => $series->id]);

    expect($series->items()->pluck('id'))->toContain($book->id, $film->id);
    expect($series->items()->distinct()->count('collection_id'))->toBe(2);
});

it('encrypts the name and description at rest', function () {
    $series = Series::factory()->create(['name' => 'Harry Potter', 'description' => 'The wizarding world.']);

    $rawName = DB::table('series')->where('id', $series->id)->value('name');
    $rawDescription = DB::table('series')->where('id', $series->id)->value('description');

    $this->assertNotSame('Harry Potter', $rawName);
    expect(decrypt($rawName, false))->toBe('Harry Potter');
    expect(decrypt($rawDescription, false))->toBe('The wizarding world.');
});

it('allows a series without a description', function () {
    $series = Series::factory()->create(['description' => null]);

    expect($series->refresh()->description)->toBeNull();
});
