<?php

declare(strict_types=1);
use App\Actions\UpdateDocument;
use App\Enums\DocumentType;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

if (! function_exists('documentForAccount')) {
    function documentForAccount(int $accountId): Document
    {
        $collection = Collection::factory()->create(['account_id' => $accountId]);
        $item = Item::factory()->create(['collection_id' => $collection->id]);
        $copy = Copy::factory()->create(['item_id' => $item->id]);

        return Document::factory()->for($copy, 'documentable')->create(['account_id' => $accountId]);
    }
}

it('updates the details of a document', function () {
    Queue::fake();

    $user = $this->createUser();
    $document = documentForAccount($user->account_id);

    $updated = new UpdateDocument(
        user: $user,
        document: $document,
        type: DocumentType::Appraisal,
        name: 'Appraisal report',
        description: 'Signed by the appraiser.',
        referenceNumber: 'AP-2024-1',
    )->execute();

    expect($updated->type)->toBe(DocumentType::Appraisal);
    expect($updated->name)->toBe('Appraisal report');
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
    expect($document->refresh()->reference_number)->toBe('AP-2024-1');
    expect($document->updated_by_id)->toBe($user->id);
});

it('forbids a user who cannot manage the account', function () {
    $user = $this->createUser();
    $document = documentForAccount($this->createAccount()->id);

    expect(fn () => new UpdateDocument(
        user: $user,
        document: $document,
        type: DocumentType::Other,
        name: 'Renamed',
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('logs the update', function () {
    Queue::fake();

    $user = $this->createUser();
    $document = documentForAccount($user->account_id);

    new UpdateDocument(user: $user, document: $document, type: DocumentType::Other, name: 'x')->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::DocumentUpdate);
});
