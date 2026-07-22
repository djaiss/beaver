<?php

declare(strict_types=1);
use App\Actions\CreateProvenanceEvent;
use App\Enums\DatePrecision;
use App\Enums\ItemActionEnum;
use App\Enums\ProvenanceEventType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * A copy sitting in a collection of the given user's account.
 */
function copyForProvenanceAction(User $user): Copy
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('records a provenance event', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);

    $event = new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Acquisition,
        title: 'Acquired at the Central Perk sale',
        occurredAtPrecision: DatePrecision::Exact,
        occurredAt: '1987-03-12',
        fromParty: 'Gunther',
        toParty: 'Ross Geller',
        location: 'New York',
    )->execute();

    expect($event)->toBeInstanceOf(ProvenanceEvent::class);
    expect($event->title)->toBe('Acquired at the Central Perk sale');
    expect($event->occurred_at->toDateString())->toBe('1987-03-12');
    expect($event->from_party)->toBe('Gunther');
    expect($event->is_verified)->toBeFalse();
});

// An undated event keeps no date at all, so nothing is left behind for a later
// edit to start reading as though the date were known.
it('stores no date when the precision says the date is unknown', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);

    $event = new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Origin,
        title: 'Made in an unrecorded year',
        occurredAtPrecision: DatePrecision::Unknown,
        occurredAt: '1987-03-12',
    )->execute();

    expect($event->occurred_at)->toBeNull();
    expect($event->formattedDate())->toBe('Date unknown');
});

// A note about how something was verified means nothing when it was not.
it('drops the verification note when the event is not verified', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);

    $event = new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Authentication,
        title: 'Authenticated',
        isVerified: false,
        verificationNote: 'Certificate held on file.',
    )->execute();

    expect($event->verification_note)->toBeNull();
});

it('keeps the verification note when the event is verified', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);

    $event = new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Authentication,
        title: 'Authenticated',
        isVerified: true,
        verificationNote: 'Certificate held on file.',
    )->execute();

    expect($event->is_verified)->toBeTrue();
    expect($event->verification_note)->toBe('Certificate held on file.');
});

it('links to a transaction of the same copy', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $event = new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Acquisition,
        title: 'Bought at auction',
        transaction: $transaction,
    )->execute();

    expect($event->transaction_id)->toBe($transaction->id);
});

// Linking across copies would attach the money of one object to the story of
// another.
it('refuses a transaction belonging to another copy', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);
    $stranger = Transaction::factory()->create(['copy_id' => copyForProvenanceAction($ross)->id]);

    new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Acquisition,
        title: 'Bought at auction',
        transaction: $stranger,
    )->execute();
})->throws(ModelNotFoundException::class);

// A transaction is one exchange, so it cannot be the source of two separate
// moments in the story.
it('refuses a transaction that already has an event', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'transaction_id' => $transaction->id]);

    new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Acquisition,
        title: 'Bought again somehow',
        transaction: $transaction,
    )->execute();
})->throws(ModelNotFoundException::class);

it('stamps who created it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);

    $event = new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Origin,
        title: 'Printed',
    )->execute();

    expect($event->created_by_id)->toBe($ross->id);
    expect($event->created_by_name)->toBe($ross->getFullName());
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);

    new CreateProvenanceEvent(
        user: $ross,
        copy: $copy,
        type: ProvenanceEventType::Origin,
        title: 'Printed',
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ProvenanceEventCreation);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::ProvenanceEventCreation);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);
    $gunther = $this->createUser();

    new CreateProvenanceEvent(
        user: $gunther,
        copy: $copy,
        type: ProvenanceEventType::Origin,
        title: 'Printed',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForProvenanceAction($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new CreateProvenanceEvent(
        user: $phoebe,
        copy: $copy,
        type: ProvenanceEventType::Origin,
        title: 'Printed',
    )->execute();
})->throws(ModelNotFoundException::class);
