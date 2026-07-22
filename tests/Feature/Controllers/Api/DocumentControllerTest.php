<?php

declare(strict_types=1);
use App\Enums\DocumentType;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function copyForApiDocuments(int $accountId): Copy
{
    $collection = Collection::factory()->create(['account_id' => $accountId]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'documentable_type',
            'documentable_id',
            'document_type',
            'name',
            'external_url',
            'download_url',
            'mime_type',
            'size',
            'description',
            'issued_at',
            'reference_number',
            'created_at',
            'updated_at',
        ],
        'links' => ['self'],
    ];
});

it('lists the documents of a copy and its records', function () {
    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);
    Document::factory()->for($copy, 'documentable')->create(['account_id' => $user->account_id]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);
    Document::factory()->for($loan, 'documentable')->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/documents')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => $this->jsonStructure], 'links', 'meta']);
});

it('does not list the documents of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForApiDocuments($this->createAccount()->id);
    Document::factory()->for($copy, 'documentable')->create(['account_id' => $copy->item->collection->account_id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/documents')->assertNotFound();
});

it('shows a document', function () {
    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);
    $document = Document::factory()->for($copy, 'documentable')->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/documents/'.$document->id)
        ->assertOk()
        ->assertJsonPath('data.id', (string) $document->id)
        ->assertJsonStructure(['data' => $this->jsonStructure]);
});

it('does not show a document from another account', function () {
    $user = $this->createUser();
    $copy = copyForApiDocuments($this->createAccount()->id);
    $document = Document::factory()->for($copy, 'documentable')->create(['account_id' => $copy->item->collection->account_id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/documents/'.$document->id)->assertNotFound();
});

it('creates a document from an external url', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/documents', [
        'type' => DocumentType::Certificate->value,
        'name' => 'Certificate',
        'external_url' => 'https://example.com/cert.pdf',
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.documentable_type', 'copy')
        ->assertJsonPath('data.attributes.external_url', 'https://example.com/cert.pdf');
});

it('creates a document from an uploaded file', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));

    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->post('/api/copies/'.$copy->id.'/documents', [
        'documentable_type' => 'loan',
        'documentable_id' => (string) $loan->id,
        'type' => DocumentType::Receipt->value,
        'name' => 'Loan receipt',
        'file' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
    ], ['Accept' => 'application/json'])
        ->assertCreated()
        ->assertJsonPath('data.attributes.documentable_type', 'loan');

    $document = Document::query()->first();
    Storage::disk(config('filesystems.default'))->assertExists($document->path);
});

it('requires a name and either a file or a url', function () {
    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/documents', [
        'type' => DocumentType::Receipt->value,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'file', 'external_url']);
});

it('updates a document', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);
    $document = Document::factory()->for($copy, 'documentable')->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/documents/'.$document->id, [
        'type' => DocumentType::Appraisal->value,
        'name' => 'Appraisal report',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Appraisal report');

    expect($document->refresh()->type)->toBe(DocumentType::Appraisal);
});

it('deletes a document', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForApiDocuments($user->account_id);
    $document = Document::factory()->external()->for($copy, 'documentable')->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/documents/'.$document->id)->assertNoContent();

    $this->assertModelMissing($document);
});

it('forbids a viewer from creating a document', function () {
    $owner = $this->createUser();
    $viewer = $this->createUser();
    $this->assignUserToAccount($viewer, $owner->account, PermissionEnum::Viewer->value);
    $copy = copyForApiDocuments($owner->account_id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/documents', [
        'type' => DocumentType::Receipt->value,
        'name' => 'Nope',
        'external_url' => 'https://example.com/x.pdf',
    ])->assertNotFound();
});
