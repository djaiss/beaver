<?php

declare(strict_types=1);
use App\Actions\DestroyDocument;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

if (! function_exists('storedDocumentForAccount')) {
    function storedDocumentForAccount(int $accountId, ?string $path = null): Document
    {
        $catalog = Catalog::factory()->create(['account_id' => $accountId]);
        $item = Item::factory()->create(['catalog_id' => $catalog->id]);
        $copy = Copy::factory()->create(['item_id' => $item->id]);

        return Document::factory()->for($copy, 'documentable')->create([
            'account_id' => $accountId,
            'path' => $path,
        ]);
    }
}

it('deletes the document and removes its file from disk', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    Storage::disk(config('filesystems.default'))->put('documents/keep.pdf', 'data');

    $user = $this->createUser();
    $document = storedDocumentForAccount($user->account_id, 'documents/keep.pdf');

    new DestroyDocument(user: $user, document: $document)->execute();

    $this->assertModelMissing($document);
    Storage::disk(config('filesystems.default'))->assertMissing('documents/keep.pdf');
});

it('deletes an external document with no file to remove', function () {
    Queue::fake();

    $user = $this->createUser();
    $document = storedDocumentForAccount($user->account_id, null);
    $document->update(['external_url' => 'https://example.com/x.pdf']);

    new DestroyDocument(user: $user, document: $document)->execute();

    $this->assertModelMissing($document);
});

it('forbids a user who cannot manage the account', function () {
    $user = $this->createUser();
    $document = storedDocumentForAccount($this->createAccount()->id, null);

    expect(fn () => new DestroyDocument(user: $user, document: $document)->execute())
        ->toThrow(ModelNotFoundException::class);
});

it('logs the deletion', function () {
    Queue::fake();

    $user = $this->createUser();
    $document = storedDocumentForAccount($user->account_id, null);

    new DestroyDocument(user: $user, document: $document)->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::DocumentDeletion);
});
