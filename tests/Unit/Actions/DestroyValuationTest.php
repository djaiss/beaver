<?php

declare(strict_types=1);
use App\Actions\DestroyValuation;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * A valuation on a copy in a collection of the given user's account.
 */
function valuationToDestroy(User $user): Valuation
{
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return Valuation::factory()->create(['copy_id' => $copy->id]);
}

it('deletes a valuation', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToDestroy($ross);

    new DestroyValuation(
        user: $ross,
        valuation: $valuation,
    )->execute();

    $this->assertModelMissing($valuation);
});

// The copy reads its worth from whichever valuation is newest, so deleting the
// latest hands the current value back to the one before it.
it('hands the current value back to the previous valuation', function () {
    Queue::fake();
    $ross = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $ross->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $older = Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 10000, 'valued_at' => '2024-01-01']);
    $newer = Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 25000, 'valued_at' => '2026-01-01']);

    expect($copy->refresh()->estimatedValue())->toBe(25000);

    new DestroyValuation(
        user: $ross,
        valuation: $newer,
    )->execute();

    expect($copy->refresh()->estimatedValue())->toBe(10000);
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToDestroy($ross);

    new DestroyValuation(
        user: $ross,
        valuation: $valuation,
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ValuationDeletion);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::ValuationDeletion);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToDestroy($ross);
    $gunther = $this->createUser();

    new DestroyValuation(
        user: $gunther,
        valuation: $valuation,
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $valuation = valuationToDestroy($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new DestroyValuation(
        user: $phoebe,
        valuation: $valuation,
    )->execute();
})->throws(ModelNotFoundException::class);
