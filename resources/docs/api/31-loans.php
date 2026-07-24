<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$loan = fn (string $id, string $direction, string $status, string $party): array => [
    'type' => 'loan',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'loan_provenance_event_id' => null,
        'return_provenance_event_id' => null,
        'direction' => $direction,
        'status' => $status,
        'party' => $party,
        'purpose' => 'Retrospective exhibition, east wing.',
        'loaned_at' => 1704067200,
        'due_at' => 1719792000,
        'returned_at' => null,
        'item_condition_out_id' => '1',
        'item_condition_in_id' => null,
        'deposit_amount' => 250000,
        'deposit_currency_code' => 'USD',
        'include_in_provenance' => true,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'context' => [
        'item_name' => 'Amazing Spider-Man #1',
        'copy_identifier' => 'CBX-042',
        'collection_name' => 'My Comics',
    ],
    'links' => [
        'self' => $base.'/copies/1/loans/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of loans to return per page, between 1 and 100.',
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
    'description' => 'The ID of the copy the loan belongs to.',
];

$loanId = [
    'name' => 'loan',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the loan.',
];

$bodyParams = [
    [
        'name' => 'direction',
        'type' => 'string',
        'required' => true,
        'description' => 'Which way custody moves. One of outgoing (a piece lent out) or incoming (a piece borrowed in).',
        'example' => 'outgoing',
    ],
    [
        'name' => 'status',
        'type' => 'string',
        'required' => false,
        'description' => 'Where the loan sits in its lifecycle. One of planned, active, overdue, returned, cancelled or lost. Defaults to active. Prefer the return endpoint over setting returned by hand, and leave overdue to be reached on its own.',
        'example' => 'active',
    ],
    [
        'name' => 'party',
        'type' => 'string',
        'required' => true,
        'description' => 'Who the copy was lent to or borrowed from.',
        'example' => 'The Whitney Museum',
    ],
    [
        'name' => 'purpose',
        'type' => 'string',
        'required' => false,
        'description' => 'Why the copy was loaned.',
        'example' => 'Retrospective exhibition, east wing.',
    ],
    [
        'name' => 'loaned_at',
        'type' => 'string',
        'required' => true,
        'description' => 'The date the copy left or arrived, in YYYY-MM-DD format.',
        'example' => '2024-01-01',
    ],
    [
        'name' => 'due_at',
        'type' => 'string',
        'required' => false,
        'description' => 'When the copy is expected back, in YYYY-MM-DD format. Leave it out for an open ended loan.',
        'example' => '2024-07-01',
    ],
    [
        'name' => 'returned_at',
        'type' => 'string',
        'required' => false,
        'description' => 'When the loan was closed, in YYYY-MM-DD format. Usually set through the return endpoint rather than here.',
        'example' => '2024-06-15',
    ],
    [
        'name' => 'item_condition_out_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the condition the copy was in when it left. Must be a condition of your account.',
        'example' => '1',
    ],
    [
        'name' => 'item_condition_in_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the condition the copy was in when it came back. Must be a condition of your account.',
        'example' => '3',
    ],
    [
        'name' => 'deposit_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'A deposit held against the loan, in the smallest currency unit (e.g. cents).',
        'example' => '250000',
    ],
    [
        'name' => 'deposit_currency_code',
        'type' => 'string',
        'required' => false,
        'description' => 'The three letter currency code the deposit is expressed in. Defaults to the currency of the collection.',
        'example' => 'USD',
    ],
    [
        'name' => 'include_in_provenance',
        'type' => 'boolean',
        'required' => false,
        'description' => 'Whether the loan belongs to the object\'s story. When true, a matching provenance event is generated for the loan, and another for the return; setting it back to false removes them.',
        'example' => 'true',
    ],
];

$returnParams = [
    [
        'name' => 'returned_at',
        'type' => 'string',
        'required' => true,
        'description' => 'The date the copy came back, in YYYY-MM-DD format.',
        'example' => '2024-06-15',
    ],
    [
        'name' => 'item_condition_in_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the condition the copy came back in. Must be a condition of your account. Setting it updates the copy\'s current condition.',
        'example' => '3',
    ],
];

$directionFilter = [
    'name' => 'direction',
    'type' => 'string',
    'required' => false,
    'description' => 'Narrow the list to one direction: outgoing (lent out) or incoming (borrowed in).',
    'example' => 'outgoing',
];

$statusFilter = [
    'name' => 'status',
    'type' => 'string',
    'required' => false,
    'description' => 'Narrow the list to one status: planned, active, overdue, returned, cancelled or lost.',
    'example' => 'active',
];

return [
    'name' => 'Loans',
    'sections' => [
        [
            'id' => 'loans-index',
            'title' => 'List all loans',
            'method' => 'GET',
            'path' => '/loans',
            'examplePath' => '/loans',
            'description' => 'Retrieve every loan in your account, across all copies, newest first. This is the account-wide view the Loans section is built on. Optional filters narrow the list by direction and status.',
            'body' => [
                'A loan moves custody without moving ownership. Each entry carries a context block with the object it is about (item name, copy identifier and collection), so a list is readable without a second request per loan.',
            ],
            'permissions' => 'Any member of the account.',
            'queryParams' => [$directionFilter, $statusFilter, ...$pagination],
            'returns' => 'A paginated list of loan objects.',
            'response' => ApiDocumentation::paginated([
                $loan('2', 'outgoing', 'active', 'The Whitney Museum'),
                $loan('1', 'incoming', 'returned', 'A private collector'),
            ], '/loans'),
        ],
        [
            'id' => 'loans-list',
            'title' => 'List loans of a copy',
            'method' => 'GET',
            'path' => '/copies/{copy}/loans',
            'examplePath' => '/copies/1/loans',
            'description' => 'Retrieve the loans recorded against a copy: the pieces lent out and the pieces borrowed in. They are returned newest first.',
            'body' => [
                'A loan moves custody without moving ownership. An outgoing loan that is active or overdue means the copy is not currently in your physical custody, and the copy reads as loaned out while one is outstanding.',
                'A loan marked for provenance generates a matching provenance event for the loan and, once it is returned, another for the return, so an institutional loan or an exhibition also reads in the object\'s documented story.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of loan objects.',
            'response' => ApiDocumentation::paginated([
                $loan('2', 'outgoing', 'active', 'The Whitney Museum'),
                $loan('1', 'incoming', 'returned', 'A private collector'),
            ], '/copies/1/loans'),
        ],
        [
            'id' => 'loans-show',
            'title' => 'Get a loan',
            'label' => 'Get a loan',
            'method' => 'GET',
            'path' => '/copies/{copy}/loans/{loan}',
            'examplePath' => '/copies/1/loans/1',
            'description' => 'Retrieve a single loan of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $loanId],
            'returns' => 'A loan object, or 404 when the loan does not belong to your account.',
            'response' => ['data' => $loan('1', 'outgoing', 'active', 'The Whitney Museum')],
        ],
        [
            'id' => 'loans-create',
            'title' => 'Create a loan',
            'label' => 'Create a loan',
            'method' => 'POST',
            'path' => '/copies/{copy}/loans',
            'examplePath' => '/copies/1/loans',
            'description' => 'Record a loan against a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created loan object.',
            'responseStatus' => 201,
            'response' => ['data' => $loan('1', 'outgoing', 'active', 'The Whitney Museum')],
        ],
        [
            'id' => 'loans-update',
            'title' => 'Update a loan',
            'label' => 'Update a loan',
            'method' => 'PUT',
            'path' => '/copies/{copy}/loans/{loan}',
            'examplePath' => '/copies/1/loans/1',
            'description' => 'Update a loan. Every field is replaced, so send the ones you want to keep along with the ones you are changing.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $loanId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated loan object.',
            'response' => ['data' => $loan('1', 'outgoing', 'active', 'The Whitney Museum')],
        ],
        [
            'id' => 'loans-return',
            'title' => 'Return a loan',
            'label' => 'Return a loan',
            'method' => 'POST',
            'path' => '/copies/{copy}/loans/{loan}/return',
            'examplePath' => '/copies/1/loans/1/return',
            'description' => 'Mark an open loan as returned. This closes it with the return date and the condition it came back in, brings the copy back into your custody, and, when the loan is part of provenance, records the return in the object\'s story. A loan that is already closed returns a 404.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $loanId],
            'bodyParams' => $returnParams,
            'returns' => 'The returned loan object.',
            'response' => ['data' => $loan('1', 'outgoing', 'returned', 'The Whitney Museum')],
        ],
        [
            'id' => 'loans-destroy',
            'title' => 'Delete a loan',
            'label' => 'Delete a loan',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/loans/{loan}',
            'examplePath' => '/copies/1/loans/1',
            'description' => 'Delete a loan. Any provenance events it generated are removed with it, and a copy left with no outstanding loan comes back into your custody.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $loanId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
