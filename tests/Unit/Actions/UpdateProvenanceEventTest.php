<?php

declare(strict_types=1);
use App\Actions\UpdateProvenanceEvent;
use App\Enums\DatePrecision;
use App\Enums\ItemActionEnum;
use App\Enums\ProvenanceEventType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
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
 * An event on a copy sitting in a collection of the given user's account.
 */
function provenanceEventFor(User $user, array $attributes = []): ProvenanceEvent
{
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return ProvenanceEvent::factory()->create(['copy_id' => $copy->id, ...$attributes]);
}

it('updates a provenance event', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross, ['type' => ProvenanceEventType::Acquisition, 'title' => 'Old title']);

    $updated = new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: ProvenanceEventType::Exhibition,
        title: 'Shown at the museum',
        occurredAtPrecision: DatePrecision::Year,
        occurredAt: '2025-04-01',
        location: 'Montreal',
    )->execute();

    expect($updated->type)->toBe(ProvenanceEventType::Exhibition);
    expect($updated->title)->toBe('Shown at the museum');
    expect($updated->occurred_at_precision)->toBe(DatePrecision::Year);
    expect($updated->formattedDate())->toBe('2025');
    expect($updated->location)->toBe('Montreal');
});

// Moving an event to an unknown date must clear the date, not leave the old one
// sitting behind the precision.
it('clears the date when the precision moves to unknown', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross, ['occurred_at' => '1987-03-12', 'occurred_at_precision' => DatePrecision::Exact]);

    $updated = new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: $event->type,
        title: $event->title,
        occurredAtPrecision: DatePrecision::Unknown,
        occurredAt: '1987-03-12',
    )->execute();

    expect($updated->occurred_at)->toBeNull();
});

it('drops the verification note when the event stops being verified', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross, ['is_verified' => true, 'verification_note' => 'Certificate on file.']);

    $updated = new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: $event->type,
        title: $event->title,
        isVerified: false,
        verificationNote: 'Certificate on file.',
    )->execute();

    expect($updated->is_verified)->toBeFalse();
    expect($updated->verification_note)->toBeNull();
});

// Relinking an event to the transaction it already carries is not a clash with
// itself, so it has to be allowed.
it('lets an event keep the transaction it already carries', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross);
    $transaction = Transaction::factory()->create(['copy_id' => $event->copy_id]);
    $event->update(['transaction_id' => $transaction->id]);

    $updated = new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: $event->type,
        title: 'Retitled',
        transaction: $transaction,
    )->execute();

    expect($updated->transaction_id)->toBe($transaction->id);
});

it('refuses a transaction another event already claims', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross);
    $transaction = Transaction::factory()->create(['copy_id' => $event->copy_id]);
    ProvenanceEvent::factory()->create(['copy_id' => $event->copy_id, 'transaction_id' => $transaction->id]);

    new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: $event->type,
        title: $event->title,
        transaction: $transaction,
    )->execute();
})->throws(ModelNotFoundException::class);

it('refuses a transaction belonging to another copy', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross);
    $stranger = Transaction::factory()->create(['copy_id' => Copy::factory()->create()->id]);

    new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: $event->type,
        title: $event->title,
        transaction: $stranger,
    )->execute();
})->throws(ModelNotFoundException::class);

it('stamps who updated it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross);
    $rachel = $this->createUser(['account_id' => $ross->account_id]);

    $updated = new UpdateProvenanceEvent(
        user: $rachel,
        event: $event,
        type: $event->type,
        title: $event->title,
    )->execute();

    expect($updated->updated_by_id)->toBe($rachel->id);
});

it('records what moved in the item log', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross, ['title' => 'Old title']);

    new UpdateProvenanceEvent(
        user: $ross,
        event: $event,
        type: $event->type,
        title: 'New title',
        occurredAtPrecision: $event->occurred_at_precision,
        occurredAt: $event->occurred_at?->toDateString(),
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ProvenanceEventUpdate);
    Queue::assertPushedOn('low', LogItemAction::class, function (LogItemAction $job): bool {
        $labels = array_column($job->parameters['changes'] ?? [], 'label');

        return $job->action === ItemActionEnum::ProvenanceEventUpdate && in_array('Title', $labels, true);
    });
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross);
    $gunther = $this->createUser();

    new UpdateProvenanceEvent(
        user: $gunther,
        event: $event,
        type: $event->type,
        title: $event->title,
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventFor($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new UpdateProvenanceEvent(
        user: $phoebe,
        event: $event,
        type: $event->type,
        title: $event->title,
    )->execute();
})->throws(ModelNotFoundException::class);
