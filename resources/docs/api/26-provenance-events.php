<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$event = fn (string $id, string $type, string $title, ?int $occurredAt, string $precision, string $formattedDate, ?string $transactionId): array => [
    'type' => 'provenance_event',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'transaction_id' => $transactionId,
        'type' => $type,
        'title' => $title,
        'description' => null,
        'occurred_at' => $occurredAt,
        'occurred_at_precision' => $precision,
        'formatted_date' => $formattedDate,
        'location' => 'New York',
        'from_party' => 'Central Perk Collectibles',
        'to_party' => 'Ross Geller',
        'reference_number' => null,
        'source_url' => null,
        'is_verified' => false,
        'verification_note' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/copies/1/provenance-events/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of provenance events to return per page, between 1 and 100.',
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
    'description' => 'The ID of the copy the provenance event belongs to.',
];

$provenanceEventId = [
    'name' => 'provenanceEvent',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the provenance event.',
];

$bodyParams = [
    [
        'name' => 'type',
        'type' => 'string',
        'required' => true,
        'description' => 'What kind of moment this records. One of acquisition, sale, gift, inheritance, ownership_transfer, custody_transfer, loan, return, exhibition, authentication, appraisal, significant_restoration, origin, discovery or other.',
        'example' => 'acquisition',
    ],
    [
        'name' => 'title',
        'type' => 'string',
        'required' => true,
        'description' => 'A short summary of the moment, shown in the timeline.',
        'example' => 'Bought at the Central Perk estate sale',
    ],
    [
        'name' => 'description',
        'type' => 'string',
        'required' => false,
        'description' => 'The detail behind the title.',
        'example' => 'Sold as part of the Gunther estate, lot 42.',
    ],
    [
        'name' => 'occurred_at',
        'type' => 'string',
        'required' => false,
        'description' => 'When it happened, in YYYY-MM-DD format. Ignored when the precision is unknown, in which case no date is stored at all.',
        'example' => '1987-06-15',
    ],
    [
        'name' => 'occurred_at_precision',
        'type' => 'string',
        'required' => false,
        'description' => 'How much of the date is actually known. One of exact, month, year, approximate or unknown.',
        'default' => 'exact',
        'example' => 'year',
    ],
    [
        'name' => 'location',
        'type' => 'string',
        'required' => false,
        'description' => 'Where it happened.',
        'example' => 'New York',
    ],
    [
        'name' => 'from_party',
        'type' => 'string',
        'required' => false,
        'description' => 'Who the object came from.',
        'example' => 'Central Perk Collectibles',
    ],
    [
        'name' => 'to_party',
        'type' => 'string',
        'required' => false,
        'description' => 'Who the object went to.',
        'example' => 'Ross Geller',
    ],
    [
        'name' => 'reference_number',
        'type' => 'string',
        'required' => false,
        'description' => 'An auction lot, a certificate number or an archive reference.',
        'example' => 'LOT-1994',
    ],
    [
        'name' => 'source_url',
        'type' => 'string',
        'required' => false,
        'description' => 'A link to where the event can be checked.',
        'example' => 'https://example.com/lots/1994',
    ],
    [
        'name' => 'is_verified',
        'type' => 'boolean',
        'required' => false,
        'description' => 'Whether evidence backs this event.',
        'default' => 'false',
        'example' => 'true',
    ],
    [
        'name' => 'verification_note',
        'type' => 'string',
        'required' => false,
        'description' => 'How it was verified. Dropped when is_verified is false, since a note about how something was verified means nothing when it was not.',
        'example' => 'Certificate held on file.',
    ],
    [
        'name' => 'transaction_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The transaction this event came from. It must belong to the same copy and must not already carry another event, otherwise the request returns 404.',
        'example' => '1',
    ],
];

$notes = [
    'No amounts live here. Prices, taxes, fees and shipping belong to the transaction and are never duplicated onto the event, so an event that came from an exchange sets `transaction_id` and you read the money from the transaction itself.',
    'A transaction is one exchange, so it can be the source of at most one event. Deleting a linked transaction unlinks the event rather than deleting it: the moment in the object story outlives the record of what was paid for it.',
    'Provenance dates are frequently uncertain, so every date is paired with `occurred_at_precision`. A precision of unknown stores no date at all, and `formatted_date` is the read only rendering of the date at its precision, which is what you should show rather than formatting `occurred_at` yourself.',
];

return [
    'name' => 'Provenance events',
    'sections' => [
        [
            'id' => 'provenance-events-list',
            'title' => 'List provenance events',
            'method' => 'GET',
            'path' => '/copies/{copy}/provenance-events',
            'examplePath' => '/copies/1/provenance-events',
            'description' => 'Retrieve the documented story of a copy: how it was acquired, who held it, where it was shown, when it was authenticated. Provenance reads as a narrative rather than as a feed, so events are returned oldest first, and an undated event sorts to the front.',
            'body' => $notes,
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of provenance event objects.',
            'response' => ApiDocumentation::paginated([
                $event('1', 'origin', 'Printed in New York', 550972800, 'year', '1987', null),
                $event('2', 'acquisition', 'Bought at the Central Perk estate sale', 1752537600, 'exact', 'July 15, 2025', '1'),
            ], '/copies/1/provenance-events'),
        ],
        [
            'id' => 'provenance-events-show',
            'title' => 'Get a provenance event',
            'label' => 'Get a provenance event',
            'method' => 'GET',
            'path' => '/copies/{copy}/provenance-events/{provenanceEvent}',
            'examplePath' => '/copies/1/provenance-events/1',
            'description' => 'Retrieve a single provenance event of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $provenanceEventId],
            'returns' => 'A provenance event object, or 404 when the event does not belong to your account.',
            'response' => ['data' => $event('1', 'origin', 'Printed in New York', 550972800, 'year', '1987', null)],
        ],
        [
            'id' => 'provenance-events-create',
            'title' => 'Create a provenance event',
            'label' => 'Create a provenance event',
            'method' => 'POST',
            'path' => '/copies/{copy}/provenance-events',
            'examplePath' => '/copies/1/provenance-events',
            'description' => 'Record a moment in the story of a copy.',
            'body' => $notes,
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created provenance event object.',
            'responseStatus' => 201,
            'response' => ['data' => $event('1', 'origin', 'Printed in New York', 550972800, 'year', '1987', null)],
        ],
        [
            'id' => 'provenance-events-update',
            'title' => 'Update a provenance event',
            'label' => 'Update a provenance event',
            'method' => 'PUT',
            'path' => '/copies/{copy}/provenance-events/{provenanceEvent}',
            'examplePath' => '/copies/1/provenance-events/1',
            'description' => 'Update a provenance event. Every field is replaced, so send the ones you want to keep along with the ones you are changing. Relinking an event to the transaction it already carries is allowed.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $provenanceEventId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated provenance event object.',
            'response' => ['data' => $event('1', 'origin', 'Printed in New York', 550972800, 'year', '1987', null)],
        ],
        [
            'id' => 'provenance-events-destroy',
            'title' => 'Delete a provenance event',
            'label' => 'Delete a provenance event',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/provenance-events/{provenanceEvent}',
            'examplePath' => '/copies/1/provenance-events/1',
            'description' => 'Delete a provenance event. The transaction it was linked to, if any, is left untouched.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $provenanceEventId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
