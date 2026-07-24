<?php

declare(strict_types=1);
use App\Actions\UploadDocument;
use App\Enums\DocumentType;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

if (! function_exists('copyForDocument')) {
    function copyForDocument(int $accountId): Copy
    {
        $catalog = Catalog::factory()->create(['account_id' => $accountId]);
        $item = Item::factory()->create(['catalog_id' => $catalog->id]);

        return Copy::factory()->create(['item_id' => $item->id]);
    }
}

it('stores an uploaded file and records its details', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));

    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);
    $file = UploadedFile::fake()->create('receipt.pdf', 120, 'application/pdf');

    $document = new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Receipt,
        name: 'Purchase receipt',
        file: $file,
    )->execute();

    expect($document)->toBeInstanceOf(Document::class);
    expect($document->path)->not->toBeNull();
    expect($document->mime_type)->toBe('application/pdf');
    expect($document->external_url)->toBeNull();
    expect($document->account_id)->toBe($user->account_id);
    expect($document->created_by_id)->toBe($user->id);
    Storage::disk(config('filesystems.default'))->assertExists($document->path);
});

it('attaches an external url without a file', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);

    $document = new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Certificate,
        name: 'Certificate',
        externalUrl: 'https://example.com/cert.pdf',
    )->execute();

    expect($document->path)->toBeNull();
    expect($document->external_url)->toBe('https://example.com/cert.pdf');
    expect($document->isExternal())->toBeTrue();
});

it('attaches a document to a record on the copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);

    $document = new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $loan,
        type: DocumentType::Correspondence,
        name: 'Loan agreement',
        externalUrl: 'https://example.com/loan.pdf',
    )->execute();

    expect($document->documentable_type)->toBe('loan');
    expect($document->documentable_id)->toBe($loan->id);
});

it('requires either a file or a url', function () {
    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);

    expect(fn () => new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Other,
        name: 'Nothing',
    )->execute())->toThrow(InvalidArgumentException::class);
});

it('rejects a file whose type is not allowed', function () {
    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);
    $file = UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload');

    expect(fn () => new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Other,
        name: 'Bad file',
        file: $file,
    )->execute())->toThrow(InvalidArgumentException::class);
});

it('rejects a file larger than the allowed size', function () {
    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);
    $file = UploadedFile::fake()->create('huge.pdf', (int) config('documents.max_size_in_kilobytes') + 1, 'application/pdf');

    expect(fn () => new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Other,
        name: 'Huge file',
        file: $file,
    )->execute())->toThrow(InvalidArgumentException::class);
});

it('forbids a user who cannot manage the account', function () {
    $user = $this->createUser();
    $copy = copyForDocument($this->createAccount()->id);

    expect(fn () => new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Other,
        name: 'Sneaky',
        externalUrl: 'https://example.com/x.pdf',
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('logs the upload', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForDocument($user->account_id);

    new UploadDocument(
        user: $user,
        copy: $copy,
        documentable: $copy,
        type: DocumentType::Other,
        name: 'A doc',
        externalUrl: 'https://example.com/x.pdf',
    )->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::DocumentUpload);
    Queue::assertPushed(LogItemAction::class);
});
