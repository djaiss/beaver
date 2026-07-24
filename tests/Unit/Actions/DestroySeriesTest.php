<?php

declare(strict_types=1);
use App\Actions\DestroySeries;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Series;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a series', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $series = Series::factory()->create(['account_id' => $account->id]);

    new DestroySeries(
        user: $editor,
        series: $series,
    )->execute();

    $this->assertModelMissing($series);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SeriesDeletion,
    );
});

it('unlinks its items rather than deleting them', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $series = Series::factory()->create(['account_id' => $account->id]);

    // A series spans collections, so the unlinking has to reach across them too.
    $books = Catalog::factory()->create(['account_id' => $account->id]);
    $films = Catalog::factory()->create(['account_id' => $account->id]);
    $book = Item::factory()->create(['catalog_id' => $books->id, 'series_id' => $series->id]);
    $film = Item::factory()->create(['catalog_id' => $films->id, 'series_id' => $series->id]);

    new DestroySeries(
        user: $owner,
        series: $series,
    )->execute();

    $this->assertModelExists($book);
    $this->assertModelExists($film);
    expect($book->refresh()->series_id)->toBeNull();
    expect($film->refresh()->series_id)->toBeNull();
});

it('unlinks a trashed item too, so restoring it does not bring back a dangling series', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $series = Series::factory()->create(['account_id' => $account->id]);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'series_id' => $series->id]);
    $item->delete();

    new DestroySeries(
        user: $owner,
        series: $series,
    )->execute();

    expect(Item::withTrashed()->find($item->id)->series_id)->toBeNull();
});

it('leaves the items of another series alone', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $doomed = Series::factory()->create(['account_id' => $account->id]);
    $spared = Series::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['series_id' => $spared->id]);

    new DestroySeries(
        user: $owner,
        series: $doomed,
    )->execute();

    expect($item->refresh()->series_id)->toBe($spared->id);
});

it('refuses a viewer', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $series = Series::factory()->create(['account_id' => $account->id]);

    new DestroySeries(
        user: $viewer,
        series: $series,
    )->execute();
})->throws(ModelNotFoundException::class);

it('refuses someone from another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $series = Series::factory()->create(['account_id' => $account->id]);
    $stranger = $this->createUser();

    new DestroySeries(
        user: $stranger,
        series: $series,
    )->execute();
})->throws(ModelNotFoundException::class);
