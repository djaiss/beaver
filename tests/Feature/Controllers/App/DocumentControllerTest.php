<?php

declare(strict_types=1);
use App\Enums\DocumentType;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function copyForDocumentController(int $accountId): Copy
{
    $catalog = Catalog::factory()->create(['account_id' => $accountId]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('shows the documents section of a copy', function () {
    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    Document::factory()->for($copy, 'documentable')->create(['account_id' => $user->account_id, 'name' => 'Provenance file']);

    $this->actingAs($user)
        ->get(route('items.history.show', [$copy->item->catalog, $copy->item, $copy, 'documents']))
        ->assertOk()
        ->assertSee('Provenance file');
});

it('attaches an uploaded file to a copy', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));

    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $catalog = $copy->item->catalog;

    $response = $this->actingAs($user)->post(route('documents.create', [$catalog, $copy->item, $copy]), [
        'documentable_type' => 'copy',
        'documentable_id' => (string) $copy->id,
        'type' => DocumentType::Receipt->value,
        'name' => 'Purchase receipt',
        'file' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect(route('items.history.show', [$catalog, $copy->item, $copy, 'documents']));
    $response->assertSessionHas('status', 'Document attached');

    $document = Document::query()->first();
    expect($document->name)->toBe('Purchase receipt');
    expect($document->documentable_type)->toBe('copy');
    Storage::disk(config('filesystems.default'))->assertExists($document->path);
});

it('attaches an external url to a record on the copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);
    $catalog = $copy->item->catalog;

    $this->actingAs($user)->post(route('documents.create', [$catalog, $copy->item, $copy]), [
        'documentable_type' => 'loan',
        'documentable_id' => (string) $loan->id,
        'type' => DocumentType::Correspondence->value,
        'name' => 'Loan agreement',
        'external_url' => 'https://example.com/loan.pdf',
    ])->assertRedirect(route('items.history.show', [$catalog, $copy->item, $copy, 'loans']));

    $document = Document::query()->first();
    expect($document->documentable_type)->toBe('loan');
    expect($document->documentable_id)->toBe($loan->id);
});

it('requires a name and either a file or a url', function () {
    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $catalog = $copy->item->catalog;

    $this->actingAs($user)->post(route('documents.create', [$catalog, $copy->item, $copy]), [
        'documentable_type' => 'copy',
        'documentable_id' => (string) $copy->id,
        'type' => DocumentType::Receipt->value,
    ])->assertSessionHasErrors(['name', 'file', 'external_url']);
});

it('does not attach a document to a record from another copy', function () {
    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $otherCopy = copyForDocumentController($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $otherCopy->id]);
    $catalog = $copy->item->catalog;

    $this->actingAs($user)->post(route('documents.create', [$catalog, $copy->item, $copy]), [
        'documentable_type' => 'loan',
        'documentable_id' => (string) $loan->id,
        'type' => DocumentType::Receipt->value,
        'name' => 'Sneaky',
        'external_url' => 'https://example.com/x.pdf',
    ])->assertNotFound();
});

it('updates a document', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $document = Document::factory()->for($copy, 'documentable')->create(['account_id' => $user->account_id]);
    $catalog = $copy->item->catalog;

    $this->actingAs($user)->put(route('documents.update', [$catalog, $copy->item, $copy, $document]), [
        'type' => DocumentType::Appraisal->value,
        'name' => 'Appraisal report',
    ])->assertRedirect(route('items.history.show', [$catalog, $copy->item, $copy, 'documents']));

    expect($document->refresh()->name)->toBe('Appraisal report');
    expect($document->type)->toBe(DocumentType::Appraisal);
});

it('deletes a document', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $document = Document::factory()->external()->for($copy, 'documentable')->create(['account_id' => $user->account_id]);
    $catalog = $copy->item->catalog;

    $this->actingAs($user)->delete(route('documents.destroy', [$catalog, $copy->item, $copy, $document]))
        ->assertRedirect(route('items.history.show', [$catalog, $copy->item, $copy, 'documents']));

    $this->assertModelMissing($document);
});

it('does not update a document from another account', function () {
    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $foreignCopy = copyForDocumentController($this->createAccount()->id);
    $foreignDocument = Document::factory()->for($foreignCopy, 'documentable')->create(['account_id' => $foreignCopy->item->catalog->account_id]);
    $catalog = $copy->item->catalog;

    $this->actingAs($user)->put(route('documents.update', [$catalog, $copy->item, $copy, $foreignDocument]), [
        'type' => DocumentType::Other->value,
        'name' => 'Hijacked',
    ])->assertNotFound();
});

it('forbids a viewer from attaching a document', function () {
    $owner = $this->createUser();
    $viewer = $this->createUser();
    $this->assignUserToAccount($viewer, $owner->account, PermissionEnum::Viewer->value);
    $copy = copyForDocumentController($owner->account_id);
    $catalog = $copy->item->catalog;

    $this->actingAs($viewer)->post(route('documents.create', [$catalog, $copy->item, $copy]), [
        'documentable_type' => 'copy',
        'documentable_id' => (string) $copy->id,
        'type' => DocumentType::Receipt->value,
        'name' => 'Nope',
        'external_url' => 'https://example.com/x.pdf',
    ])->assertNotFound();
});

it('streams a stored document to a member of the account', function () {
    Storage::fake(config('filesystems.default'));
    Storage::disk(config('filesystems.default'))->put('documents/file.pdf', 'the bytes');

    $user = $this->createUser();
    $copy = copyForDocumentController($user->account_id);
    $document = Document::factory()->for($copy, 'documentable')->create([
        'account_id' => $user->account_id,
        'path' => 'documents/file.pdf',
        'mime_type' => 'application/pdf',
    ]);

    $this->actingAs($user)->get(route('documents.show', $document))->assertOk();
});

it('does not stream a document from another account', function () {
    Storage::fake(config('filesystems.default'));

    $user = $this->createUser();
    $foreignCopy = copyForDocumentController($this->createAccount()->id);
    $document = Document::factory()->for($foreignCopy, 'documentable')->create([
        'account_id' => $foreignCopy->item->catalog->account_id,
        'path' => 'documents/file.pdf',
    ]);

    $this->actingAs($user)->get(route('documents.show', $document))->assertNotFound();
});
