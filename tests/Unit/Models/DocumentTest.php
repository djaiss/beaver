<?php

declare(strict_types=1);
use App\Enums\DocumentType;
use App\Models\Account;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $document = Document::factory()->create();

    expect($document->account()->exists())->toBeTrue();
    expect($document->account)->toBeInstanceOf(Account::class);
});

it('is attached to its documentable through the morph map', function () {
    $copy = Copy::factory()->create();
    $document = Document::factory()->for($copy, 'documentable')->create();

    expect($document->documentable_type)->toBe('copy');
    expect($document->documentable)->toBeInstanceOf(Copy::class);
    expect($document->documentable->id)->toBe($copy->id);
});

it('casts its type to an enum and its sensitive fields as encrypted', function () {
    $document = Document::factory()->create([
        'type' => DocumentType::Certificate,
        'name' => 'Certificate of authenticity',
        'reference_number' => 'CERT-9',
    ]);

    expect($document->type)->toBe(DocumentType::Certificate);
    expect($document->name)->toBe('Certificate of authenticity');
    expect($document->getRawOriginal('name'))->not->toBe('Certificate of authenticity');
    expect(Crypt::decryptString($document->getRawOriginal('name')))->toBe('Certificate of authenticity');
    expect(Crypt::decryptString($document->getRawOriginal('reference_number')))->toBe('CERT-9');
});

it('reaches the item behind whatever it is attached to', function () {
    $copy = Copy::factory()->create();
    $onCopy = Document::factory()->for($copy, 'documentable')->create();

    $loan = Loan::factory()->create(['copy_id' => $copy->id]);
    $onLoan = Document::factory()->for($loan, 'documentable')->create(['account_id' => $copy->item->collection->account_id]);

    expect($onCopy->item()->id)->toBe($copy->item->id);
    expect($onLoan->item()->id)->toBe($copy->item->id);
});

it('knows whether it is stored or external', function () {
    $stored = Document::factory()->create();
    $external = Document::factory()->external()->create();

    expect($stored->isStored())->toBeTrue();
    expect($stored->isExternal())->toBeFalse();
    expect($external->isStored())->toBeFalse();
    expect($external->isExternal())->toBeTrue();
});

it('points a stored document at the download route and an external one at its link', function () {
    $stored = Document::factory()->create();
    $external = Document::factory()->external()->create(['external_url' => 'https://example.com/file.pdf']);

    expect($stored->url())->toBe(route('documents.show', $stored));
    expect($external->url())->toBe('https://example.com/file.pdf');
});

it('formats its mime type and size for reading', function () {
    $document = Document::factory()->create(['mime_type' => 'application/pdf', 'size' => 2_600_000]);
    $external = Document::factory()->external()->create();

    expect($document->format())->toBe('PDF');
    expect($document->humanSize())->toBe('2.5 MB');
    expect($external->format())->toBeNull();
    expect($external->humanSize())->toBeNull();
});

it('lists the documents attached to a record newest issue first', function () {
    $copy = Copy::factory()->create();
    Document::factory()->for($copy, 'documentable')->create(['issued_at' => '2020-01-01']);
    $newer = Document::factory()->for($copy, 'documentable')->create(['issued_at' => '2024-01-01']);

    expect($copy->documents()->count())->toBe(2);
    expect($copy->documents()->first()->id)->toBe($newer->id);
});

it('purges the documents and their files when its record is permanently deleted', function () {
    Storage::fake(config('filesystems.default'));

    $copy = Copy::factory()->create();
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);
    Storage::disk(config('filesystems.default'))->put('documents/x.pdf', 'data');
    $document = Document::factory()->for($loan, 'documentable')->create([
        'account_id' => $copy->item->collection->account_id,
        'path' => 'documents/x.pdf',
    ]);

    $loan->delete();

    $this->assertModelMissing($document);
    Storage::disk(config('filesystems.default'))->assertMissing('documents/x.pdf');
});

it('keeps documents on a soft deleted record but purges them on a force delete', function () {
    $copy = Copy::factory()->create();
    $document = Document::factory()->for($copy, 'documentable')->create();

    $copy->delete();
    expect(Document::query()->whereKey($document->id)->exists())->toBeTrue();

    $copy->forceDelete();
    $this->assertModelMissing($document);
});
