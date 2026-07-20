<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$record = fn (string $id, string $type, string $title, ?int $cost): array => [
    'type' => 'maintenance_record',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'provenance_event_id' => null,
        'type' => $type,
        'title' => $title,
        'description' => 'Surface cleaned and re-boxed in archival storage.',
        'performed_by' => 'Atelier Restauration',
        'performed_at' => 1704067200,
        'cost_amount' => $cost,
        'cost_currency_code' => 'USD',
        'condition_before_id' => '3',
        'condition_after_id' => '1',
        'next_due_at' => 1735689600,
        'include_in_provenance' => false,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/copies/1/maintenance-records/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of maintenance records to return per page, between 1 and 100.',
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
    'description' => 'The ID of the copy the maintenance record belongs to.',
];

$recordId = [
    'name' => 'maintenanceRecord',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the maintenance record.',
];

$bodyParams = [
    [
        'name' => 'type',
        'type' => 'string',
        'required' => true,
        'description' => 'What kind of work this was. One of cleaning, repair, servicing, conservation, restoration, replacement or inspection.',
        'example' => 'conservation',
    ],
    [
        'name' => 'title',
        'type' => 'string',
        'required' => true,
        'description' => 'A short summary of the work.',
        'example' => 'Archival cleaning and re-housing',
    ],
    [
        'name' => 'description',
        'type' => 'string',
        'required' => false,
        'description' => 'The detail behind the title.',
        'example' => 'Surface cleaned and re-boxed in archival storage.',
    ],
    [
        'name' => 'performed_by',
        'type' => 'string',
        'required' => false,
        'description' => 'Who performed the work.',
        'example' => 'Atelier Restauration',
    ],
    [
        'name' => 'performed_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the work was done, in YYYY-MM-DD format.',
        'example' => '2024-01-01',
    ],
    [
        'name' => 'cost_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'What the work cost, in the smallest currency unit (e.g. cents).',
        'example' => '12000',
    ],
    [
        'name' => 'cost_currency_code',
        'type' => 'string',
        'required' => false,
        'description' => 'The three letter currency code the cost is expressed in. Defaults to the currency of the collection.',
        'example' => 'USD',
    ],
    [
        'name' => 'condition_before_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the condition the copy was in before the work. Must be a condition of your account.',
        'example' => '3',
    ],
    [
        'name' => 'condition_after_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the condition the copy is in after the work. Must be a condition of your account. Setting it updates the copy\'s current condition.',
        'example' => '1',
    ],
    [
        'name' => 'next_due_at',
        'type' => 'string',
        'required' => false,
        'description' => 'When this care is next due, in YYYY-MM-DD format. Leave it out when the work is not recurring.',
        'example' => '2025-01-01',
    ],
    [
        'name' => 'include_in_provenance',
        'type' => 'boolean',
        'required' => false,
        'description' => 'Whether the work is significant enough to belong to the object\'s story. When true, a matching provenance event is generated; setting it back to false removes that event.',
        'example' => 'false',
    ],
];

return [
    'name' => 'Maintenance records',
    'sections' => [
        [
            'id' => 'maintenance-records-list',
            'title' => 'List maintenance records',
            'method' => 'GET',
            'path' => '/copies/{copy}/maintenance-records',
            'examplePath' => '/copies/1/maintenance-records',
            'description' => 'Retrieve the work logged against a copy: the cleanings, repairs, servicings, conservations, restorations, replacements and inspections it has been through. They are returned newest first.',
            'body' => [
                'Maintenance costs live on this model rather than in transactions, so a record carries its own cost and currency.',
                'A record marked for provenance generates a matching provenance event, so a significant restoration or conservation also reads in the object\'s documented story.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of maintenance record objects.',
            'response' => ApiDocumentation::paginated([
                $record('2', 'conservation', 'Archival cleaning and re-housing', 12000),
                $record('1', 'inspection', 'Annual condition check', null),
            ], '/copies/1/maintenance-records'),
        ],
        [
            'id' => 'maintenance-records-show',
            'title' => 'Get a maintenance record',
            'label' => 'Get a maintenance record',
            'method' => 'GET',
            'path' => '/copies/{copy}/maintenance-records/{maintenanceRecord}',
            'examplePath' => '/copies/1/maintenance-records/1',
            'description' => 'Retrieve a single maintenance record of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $recordId],
            'returns' => 'A maintenance record object, or 404 when the record does not belong to your account.',
            'response' => ['data' => $record('1', 'conservation', 'Archival cleaning and re-housing', 12000)],
        ],
        [
            'id' => 'maintenance-records-create',
            'title' => 'Create a maintenance record',
            'label' => 'Create a maintenance record',
            'method' => 'POST',
            'path' => '/copies/{copy}/maintenance-records',
            'examplePath' => '/copies/1/maintenance-records',
            'description' => 'Log a piece of work performed on a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created maintenance record object.',
            'responseStatus' => 201,
            'response' => ['data' => $record('1', 'conservation', 'Archival cleaning and re-housing', 12000)],
        ],
        [
            'id' => 'maintenance-records-update',
            'title' => 'Update a maintenance record',
            'label' => 'Update a maintenance record',
            'method' => 'PUT',
            'path' => '/copies/{copy}/maintenance-records/{maintenanceRecord}',
            'examplePath' => '/copies/1/maintenance-records/1',
            'description' => 'Update a maintenance record. Every field is replaced, so send the ones you want to keep along with the ones you are changing.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $recordId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated maintenance record object.',
            'response' => ['data' => $record('1', 'conservation', 'Archival cleaning and re-housing', 12000)],
        ],
        [
            'id' => 'maintenance-records-destroy',
            'title' => 'Delete a maintenance record',
            'label' => 'Delete a maintenance record',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/maintenance-records/{maintenanceRecord}',
            'examplePath' => '/copies/1/maintenance-records/1',
            'description' => 'Delete a maintenance record. Any provenance event it generated is removed with it.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $recordId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
