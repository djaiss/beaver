<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$record = fn (string $id, string $provider, int $insuredValue, string $status): array => [
    'type' => 'insurance_record',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'provider' => $provider,
        'policy_number' => 'CIS-88231',
        'coverage_type' => 'Scheduled item',
        'insured_value' => $insuredValue,
        'currency_code' => 'USD',
        'deductible_amount' => 10000,
        'deductible_currency_code' => 'USD',
        'starts_at' => 1704067200,
        'ends_at' => null,
        'status' => $status,
        'is_scheduled_item' => true,
        'contact_name' => 'Dana Whitfield',
        'contact_email' => 'dana@cisinsurance.com',
        'contact_phone' => '+1 888 837 9537',
        'note' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/copies/1/insurance-records/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of insurance records to return per page, between 1 and 100.',
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
    'description' => 'The ID of the copy the insurance record belongs to.',
];

$recordId = [
    'name' => 'insuranceRecord',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the insurance record.',
];

$bodyParams = [
    [
        'name' => 'provider',
        'type' => 'string',
        'required' => true,
        'description' => 'The insurer that holds the coverage.',
        'example' => 'Collectibles Insurance Services',
    ],
    [
        'name' => 'insured_value',
        'type' => 'integer',
        'required' => true,
        'description' => 'What the copy is insured for, in the smallest currency unit (e.g. cents).',
        'example' => '45000',
    ],
    [
        'name' => 'status',
        'type' => 'string',
        'required' => false,
        'description' => 'Where the coverage stands. One of active, expired, cancelled or pending. Defaults to active. A copy may hold only one active record per policy number at a time.',
        'example' => 'active',
    ],
    [
        'name' => 'currency_code',
        'type' => 'string',
        'required' => false,
        'description' => 'The three letter currency code the insured value is expressed in. Defaults to the currency of the collection.',
        'example' => 'USD',
    ],
    [
        'name' => 'policy_number',
        'type' => 'string',
        'required' => false,
        'description' => 'The policy number the coverage sits under.',
        'example' => 'CIS-88231',
    ],
    [
        'name' => 'coverage_type',
        'type' => 'string',
        'required' => false,
        'description' => 'What kind of coverage this is, such as a scheduled item or blanket contents.',
        'example' => 'Scheduled item',
    ],
    [
        'name' => 'deductible_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'The deductible, in the smallest currency unit.',
        'example' => '10000',
    ],
    [
        'name' => 'deductible_currency_code',
        'type' => 'string',
        'required' => false,
        'description' => 'The three letter currency code the deductible is expressed in. Defaults to the currency of the insured value.',
        'example' => 'USD',
    ],
    [
        'name' => 'starts_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the coverage begins, in YYYY-MM-DD format.',
        'example' => '2024-01-01',
    ],
    [
        'name' => 'ends_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the coverage ends, in YYYY-MM-DD format. Leave it out for ongoing coverage.',
        'example' => '2025-01-01',
    ],
    [
        'name' => 'is_scheduled_item',
        'type' => 'boolean',
        'required' => false,
        'description' => 'Whether the copy is individually listed on the policy rather than covered under blanket contents.',
        'example' => 'true',
    ],
    [
        'name' => 'contact_name',
        'type' => 'string',
        'required' => false,
        'description' => 'The broker or agent for the policy.',
        'example' => 'Dana Whitfield',
    ],
    [
        'name' => 'contact_email',
        'type' => 'string',
        'required' => false,
        'description' => 'The email of the broker or agent.',
        'example' => 'dana@cisinsurance.com',
    ],
    [
        'name' => 'contact_phone',
        'type' => 'string',
        'required' => false,
        'description' => 'The phone number of the broker or agent.',
        'example' => '+1 888 837 9537',
    ],
    [
        'name' => 'note',
        'type' => 'string',
        'required' => false,
        'description' => 'A free form note about the coverage.',
        'example' => 'On the fine-collectibles rider.',
    ],
];

return [
    'name' => 'Insurance records',
    'sections' => [
        [
            'id' => 'insurance-records-list',
            'title' => 'List insurance records',
            'method' => 'GET',
            'path' => '/copies/{copy}/insurance-records',
            'examplePath' => '/copies/1/insurance-records',
            'description' => 'Retrieve the insurance coverage held against a copy: the policies, providers and insured values it has carried. They are returned newest window first.',
            'body' => [
                'Coverage is historical. Changing the insured value records a new record rather than overwriting an old one, so a copy accumulates records as its policies and values change.',
                'A copy may hold only one active record per policy number at a time. Recording or reviving a second active record under the same policy number is rejected with a validation error.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of insurance record objects.',
            'response' => ApiDocumentation::paginated([
                $record('2', 'Collectibles Insurance Services', 45000, 'active'),
                $record('1', 'Homeowner Rider (Allstate)', 30000, 'expired'),
            ], '/copies/1/insurance-records'),
        ],
        [
            'id' => 'insurance-records-show',
            'title' => 'Get an insurance record',
            'label' => 'Get an insurance record',
            'method' => 'GET',
            'path' => '/copies/{copy}/insurance-records/{insuranceRecord}',
            'examplePath' => '/copies/1/insurance-records/1',
            'description' => 'Retrieve a single insurance record of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $recordId],
            'returns' => 'An insurance record object, or 404 when the record does not belong to your account.',
            'response' => ['data' => $record('1', 'Collectibles Insurance Services', 45000, 'active')],
        ],
        [
            'id' => 'insurance-records-create',
            'title' => 'Create an insurance record',
            'label' => 'Create an insurance record',
            'method' => 'POST',
            'path' => '/copies/{copy}/insurance-records',
            'examplePath' => '/copies/1/insurance-records',
            'description' => 'Record a piece of insurance coverage against a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created insurance record object.',
            'responseStatus' => 201,
            'response' => ['data' => $record('1', 'Collectibles Insurance Services', 45000, 'active')],
        ],
        [
            'id' => 'insurance-records-update',
            'title' => 'Update an insurance record',
            'label' => 'Update an insurance record',
            'method' => 'PUT',
            'path' => '/copies/{copy}/insurance-records/{insuranceRecord}',
            'examplePath' => '/copies/1/insurance-records/1',
            'description' => 'Update an insurance record. Every field is replaced, so send the ones you want to keep along with the ones you are changing.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $recordId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated insurance record object.',
            'response' => ['data' => $record('1', 'Collectibles Insurance Services', 45000, 'active')],
        ],
        [
            'id' => 'insurance-records-destroy',
            'title' => 'Delete an insurance record',
            'label' => 'Delete an insurance record',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/insurance-records/{insuranceRecord}',
            'examplePath' => '/copies/1/insurance-records/1',
            'description' => 'Delete an insurance record.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $recordId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
