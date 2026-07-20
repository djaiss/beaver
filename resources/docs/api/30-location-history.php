<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$record = fn (string $id, string $locationId, ?int $movedOutAt, bool $isOpen): array => [
    'type' => 'location_history',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'location_id' => $locationId,
        'moved_at' => 1704067200,
        'moved_out_at' => $movedOutAt,
        'reason' => 'Rotated into the display case',
        'note' => null,
        'is_open' => $isOpen,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/copies/1/location-history/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of records to return per page, between 1 and 100.',
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
    'description' => 'The ID of the copy the record belongs to.',
];

$recordId = [
    'name' => 'locationHistory',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the location history record.',
];

$moveParams = [
    [
        'name' => 'location_id',
        'type' => 'integer',
        'required' => true,
        'description' => 'The ID of the location the copy is moving to. Must be a location of your account.',
        'example' => '4',
    ],
    [
        'name' => 'moved_at',
        'type' => 'string',
        'required' => true,
        'description' => 'The date the copy arrived at the location, in YYYY-MM-DD format.',
        'example' => '2024-01-01',
    ],
    [
        'name' => 'reason',
        'type' => 'string',
        'required' => false,
        'description' => 'Why the copy was moved.',
        'example' => 'Rotated into the display case',
    ],
    [
        'name' => 'note',
        'type' => 'string',
        'required' => false,
        'description' => 'A free form note about the move.',
        'example' => 'Front row, eye level.',
    ],
];

$correctionParams = array_merge($moveParams, [
    [
        'name' => 'moved_out_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the copy left the location, in YYYY-MM-DD format. Leave it out for the record that is still open.',
        'example' => '2024-06-01',
    ],
]);

return [
    'name' => 'Location history',
    'sections' => [
        [
            'id' => 'location-history-list',
            'title' => 'List location history',
            'method' => 'GET',
            'path' => '/copies/{copy}/location-history',
            'examplePath' => '/copies/1/location-history',
            'description' => 'Retrieve where a copy has been stored over time: each place it lived and the period it was there. They are returned newest move first. The open record, with no moved_out_at, is where the copy is now.',
            'body' => [
                'A copy has at most one open record at a time, and its current location always mirrors that record. Ordinary movement is not provenance; a historically significant institutional movement is recorded separately as a provenance event.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of location history objects.',
            'response' => ApiDocumentation::paginated([
                $record('2', '4', null, true),
                $record('1', '2', 1704067200, false),
            ], '/copies/1/location-history'),
        ],
        [
            'id' => 'location-history-show',
            'title' => 'Get a location record',
            'label' => 'Get a location record',
            'method' => 'GET',
            'path' => '/copies/{copy}/location-history/{locationHistory}',
            'examplePath' => '/copies/1/location-history/1',
            'description' => 'Retrieve a single location history record of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $recordId],
            'returns' => 'A location history object, or 404 when the record does not belong to your account.',
            'response' => ['data' => $record('1', '4', null, true)],
        ],
        [
            'id' => 'location-history-create',
            'title' => 'Move a copy',
            'label' => 'Move a copy',
            'method' => 'POST',
            'path' => '/copies/{copy}/location-history',
            'examplePath' => '/copies/1/location-history',
            'description' => 'Move a copy to a location. This closes the copy\'s open record, opens a new one and updates its current location in one step, so the copy and its history never disagree about where it is.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $moveParams,
            'returns' => 'The new open location history record.',
            'responseStatus' => 201,
            'response' => ['data' => $record('1', '4', null, true)],
        ],
        [
            'id' => 'location-history-update',
            'title' => 'Correct a location record',
            'label' => 'Correct a location record',
            'method' => 'PUT',
            'path' => '/copies/{copy}/location-history/{locationHistory}',
            'examplePath' => '/copies/1/location-history/1',
            'description' => 'Correct a location history record that was logged wrong. Every field is replaced, so send the ones you want to keep. The copy\'s current location is recomputed from the history afterwards.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $recordId],
            'bodyParams' => $correctionParams,
            'returns' => 'The updated location history record.',
            'response' => ['data' => $record('1', '4', null, true)],
        ],
        [
            'id' => 'location-history-destroy',
            'title' => 'Delete a location record',
            'label' => 'Delete a location record',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/location-history/{locationHistory}',
            'examplePath' => '/copies/1/location-history/1',
            'description' => 'Delete a location history record. If it was the open record, the copy\'s current location falls back to what remains.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $recordId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
