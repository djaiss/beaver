<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$document = fn (string $id, string $documentableType, string $documentableId, string $name, bool $stored): array => [
    'type' => 'document',
    'id' => $id,
    'attributes' => [
        'documentable_type' => $documentableType,
        'documentable_id' => $documentableId,
        'document_type' => 'receipt',
        'name' => $name,
        'external_url' => $stored ? null : 'https://drive.example.com/appraisal.pdf',
        'download_url' => $stored ? $base.'/documents/'.$id : null,
        'mime_type' => $stored ? 'application/pdf' : null,
        'size' => $stored ? 248000 : null,
        'description' => 'Signed and dated by the appraiser.',
        'issued_at' => 1704067200,
        'reference_number' => 'INV-2024-0117',
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/documents/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of documents to return per page, between 1 and 100.',
        'default' => '10',
    ],
    [
        'name' => 'page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The page number to return.',
        'default' => '1',
    ],
];

$copyId = [
    'name' => 'copy',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the copy whose documents you are listing or adding to.',
];

$documentId = [
    'name' => 'document',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the document.',
];

$createParams = [
    [
        'name' => 'documentable_type',
        'type' => 'string',
        'required' => false,
        'description' => 'The kind of record the document is attached to. One of copy, transaction, provenance_event, valuation, insurance_record, maintenance_record or loan. Defaults to copy.',
        'example' => 'valuation',
    ],
    [
        'name' => 'documentable_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the record the document is attached to. Must belong to the copy in the path. Defaults to the copy itself.',
        'example' => '5',
    ],
    [
        'name' => 'type',
        'type' => 'string',
        'required' => true,
        'description' => 'What the document is. One of receipt, invoice, certificate, appraisal, insurance, photograph, condition_report, restoration_report, catalogue, correspondence, ownership_record, authenticity_record or other.',
        'example' => 'appraisal',
    ],
    [
        'name' => 'name',
        'type' => 'string',
        'required' => true,
        'description' => 'The display name of the document.',
        'example' => 'Appraisal report',
    ],
    [
        'name' => 'file',
        'type' => 'file',
        'required' => false,
        'description' => 'The file to store, sent as multipart/form-data. Provide either a file or an external_url, not both. A stored file may be up to 20 MB and must be a PDF, an image, or a common document or spreadsheet format.',
        'example' => '@appraisal.pdf',
    ],
    [
        'name' => 'external_url',
        'type' => 'string',
        'required' => false,
        'description' => 'A link to a file held elsewhere, used instead of uploading one. Provide either a file or an external_url.',
        'example' => 'https://drive.example.com/appraisal.pdf',
    ],
    [
        'name' => 'description',
        'type' => 'string',
        'required' => false,
        'description' => 'A free note about the document.',
        'example' => 'Signed and dated by the appraiser.',
    ],
    [
        'name' => 'issued_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the document was issued, in YYYY-MM-DD format.',
        'example' => '2024-01-01',
    ],
    [
        'name' => 'reference_number',
        'type' => 'string',
        'required' => false,
        'description' => 'An external reference such as an invoice or certificate number.',
        'example' => 'INV-2024-0117',
    ],
];

$updateParams = [
    [
        'name' => 'type',
        'type' => 'string',
        'required' => true,
        'description' => 'What the document is. One of receipt, invoice, certificate, appraisal, insurance, photograph, condition_report, restoration_report, catalogue, correspondence, ownership_record, authenticity_record or other.',
        'example' => 'appraisal',
    ],
    [
        'name' => 'name',
        'type' => 'string',
        'required' => true,
        'description' => 'The display name of the document.',
        'example' => 'Appraisal report',
    ],
    [
        'name' => 'description',
        'type' => 'string',
        'required' => false,
        'description' => 'A free note about the document.',
        'example' => 'Signed and dated by the appraiser.',
    ],
    [
        'name' => 'issued_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the document was issued, in YYYY-MM-DD format.',
        'example' => '2024-01-01',
    ],
    [
        'name' => 'reference_number',
        'type' => 'string',
        'required' => false,
        'description' => 'An external reference such as an invoice or certificate number.',
        'example' => 'INV-2024-0117',
    ],
];

return [
    'name' => 'Documents',
    'sections' => [
        [
            'id' => 'documents-list',
            'title' => 'List documents',
            'method' => 'GET',
            'path' => '/copies/{copy}/documents',
            'examplePath' => '/copies/1/documents',
            'description' => 'Retrieve the documents attached to a copy and to every record on it: its transactions, provenance events, valuations, insurance records, maintenance records and loans. Each document names the record it hangs from through documentable_type and documentable_id.',
            'body' => [
                'A document is either a file stored with us or a link to one held elsewhere. A stored file is never served from a public URL; reach it through the download_url, which streams it to members of the account.',
                'Deleting a document removes its stored file too, and deleting a record removes the documents attached to it.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of document objects.',
            'response' => ApiDocumentation::paginated([
                $document('2', 'valuation', '5', 'Appraisal report', true),
                $document('1', 'copy', '1', 'Certificate of authenticity', false),
            ], '/copies/1/documents'),
        ],
        [
            'id' => 'documents-create',
            'title' => 'Add a document',
            'label' => 'Add a document',
            'method' => 'POST',
            'path' => '/copies/{copy}/documents',
            'examplePath' => '/copies/1/documents',
            'description' => 'Attach a document to a copy or one of its records, by uploading a file as multipart/form-data or by pointing at an external URL. Provide exactly one of the two.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $createParams,
            'returns' => 'The created document object.',
            'responseStatus' => 201,
            'response' => ['data' => $document('1', 'valuation', '5', 'Appraisal report', true)],
        ],
        [
            'id' => 'documents-show',
            'title' => 'Get a document',
            'label' => 'Get a document',
            'method' => 'GET',
            'path' => '/documents/{document}',
            'examplePath' => '/documents/1',
            'description' => 'Retrieve a single document by its ID. The stored disk path is never exposed; a stored file is reached through its download_url.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$documentId],
            'returns' => 'A document object, or 404 when the document does not belong to your account.',
            'response' => ['data' => $document('1', 'valuation', '5', 'Appraisal report', true)],
        ],
        [
            'id' => 'documents-update',
            'title' => 'Update a document',
            'label' => 'Update a document',
            'method' => 'PUT',
            'path' => '/documents/{document}',
            'examplePath' => '/documents/1',
            'description' => 'Update a document\'s details. The stored file or the external link itself does not change here; to replace what is stored, delete the document and add a fresh one. Every field is replaced, so send the ones you want to keep along with the ones you are changing.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$documentId],
            'bodyParams' => $updateParams,
            'returns' => 'The updated document object.',
            'response' => ['data' => $document('1', 'valuation', '5', 'Appraisal report', true)],
        ],
        [
            'id' => 'documents-destroy',
            'title' => 'Delete a document',
            'label' => 'Delete a document',
            'method' => 'DELETE',
            'path' => '/documents/{document}',
            'examplePath' => '/documents/1',
            'description' => 'Delete a document. If it is a stored file, the file is removed from disk as well. This cannot be undone.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$documentId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
