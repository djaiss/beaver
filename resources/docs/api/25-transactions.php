<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$transaction = fn (string $id, string $type, ?int $amount, ?int $totalAmount, ?int $total): array => [
    'type' => 'transaction',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'type' => $type,
        'counterparty' => 'Central Perk Collectibles',
        'amount' => $amount,
        'currency_code' => 'USD',
        'tax_amount' => null,
        'fee_amount' => null,
        'shipping_amount' => null,
        'total_amount' => $totalAmount,
        'total' => $total,
        'occurred_at' => 1752537600,
        'reference_number' => null,
        'source_url' => null,
        'note' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/copies/1/transactions/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of transactions to return per page, between 1 and 100.',
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
    'description' => 'The ID of the copy the transaction belongs to.',
];

$transactionId = [
    'name' => 'transaction',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the transaction.',
];

$bodyParams = [
    [
        'name' => 'type',
        'type' => 'string',
        'required' => true,
        'description' => 'What kind of exchange this records. One of purchase, sale, trade, gift_received, gift_given, inheritance, refund, fee, tax, shipping or other.',
        'example' => 'purchase',
    ],
    [
        'name' => 'occurred_at',
        'type' => 'string',
        'required' => true,
        'description' => 'The date the exchange happened, in YYYY-MM-DD format.',
        'example' => '2024-01-15',
    ],
    [
        'name' => 'counterparty',
        'type' => 'string',
        'required' => false,
        'description' => 'Who was on the other side of the exchange, such as the seller or the buyer.',
        'example' => 'Central Perk Collectibles',
    ],
    [
        'name' => 'amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'The headline price, in the smallest currency unit (e.g. cents), before tax, fees and shipping.',
        'example' => '5000',
    ],
    [
        'name' => 'currency_code',
        'type' => 'string',
        'required' => false,
        'description' => 'The three letter currency code every amount on the transaction is expressed in. Defaults to the currency of the collection.',
        'example' => 'USD',
    ],
    [
        'name' => 'tax_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'The tax paid, in the smallest currency unit.',
        'example' => '400',
    ],
    [
        'name' => 'fee_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'The fees paid, in the smallest currency unit.',
        'example' => '250',
    ],
    [
        'name' => 'shipping_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'The shipping paid, in the smallest currency unit.',
        'example' => '900',
    ],
    [
        'name' => 'total_amount',
        'type' => 'integer',
        'required' => false,
        'description' => 'What actually changed hands, in the smallest currency unit. Leave it out and the total is read as the sum of the amount, the tax, the fees and the shipping.',
        'example' => '6550',
    ],
    [
        'name' => 'reference_number',
        'type' => 'string',
        'required' => false,
        'description' => 'An order or invoice number for the exchange.',
        'example' => 'INV-1994',
    ],
    [
        'name' => 'source_url',
        'type' => 'string',
        'required' => false,
        'description' => 'A link to the listing or the receipt the exchange came from.',
        'example' => 'https://example.com/listings/1994',
    ],
    [
        'name' => 'note',
        'type' => 'string',
        'required' => false,
        'description' => 'A free form note about the exchange.',
        'example' => 'Haggled down at the flea market.',
    ],
];

return [
    'name' => 'Transactions',
    'sections' => [
        [
            'id' => 'transactions-list',
            'title' => 'List transactions',
            'method' => 'GET',
            'path' => '/copies/{copy}/transactions',
            'examplePath' => '/copies/1/transactions',
            'description' => 'Retrieve the transactions recorded against a copy: purchases, sales, trades, gifts, refunds and the fees, taxes and shipping around them. They are returned most recent first.',
            'body' => [
                'Transactions are the single source of truth for commercial data. The acquisition date and the price paid of a copy are read from the earliest transaction that brought it into the collection rather than stored on the copy itself.',
                'Every transaction carries two totals. `total_amount` is what you recorded and may be null; `total` is what actually changed hands, falling back to the sum of the amount, the tax, the fees and the shipping when you did not record one. Read `total` unless you specifically want to know whether a total was given.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of transaction objects.',
            'response' => ApiDocumentation::paginated([
                $transaction('1', 'purchase', 5000, 6550, 6550),
                $transaction('2', 'shipping', 900, null, 900),
            ], '/copies/1/transactions'),
        ],
        [
            'id' => 'transactions-show',
            'title' => 'Get a transaction',
            'label' => 'Get a transaction',
            'method' => 'GET',
            'path' => '/copies/{copy}/transactions/{transaction}',
            'examplePath' => '/copies/1/transactions/1',
            'description' => 'Retrieve a single transaction of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $transactionId],
            'returns' => 'A transaction object, or 404 when the transaction does not belong to your account.',
            'response' => ['data' => $transaction('1', 'purchase', 5000, 6550, 6550)],
        ],
        [
            'id' => 'transactions-create',
            'title' => 'Create a transaction',
            'label' => 'Create a transaction',
            'method' => 'POST',
            'path' => '/copies/{copy}/transactions',
            'examplePath' => '/copies/1/transactions',
            'description' => 'Record a transaction against a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created transaction object.',
            'responseStatus' => 201,
            'response' => ['data' => $transaction('1', 'purchase', 5000, 6550, 6550)],
        ],
        [
            'id' => 'transactions-update',
            'title' => 'Update a transaction',
            'label' => 'Update a transaction',
            'method' => 'PUT',
            'path' => '/copies/{copy}/transactions/{transaction}',
            'examplePath' => '/copies/1/transactions/1',
            'description' => 'Update a transaction. Every field is replaced, so send the ones you want to keep along with the ones you are changing.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $transactionId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated transaction object.',
            'response' => ['data' => $transaction('1', 'purchase', 5000, 6550, 6550)],
        ],
        [
            'id' => 'transactions-destroy',
            'title' => 'Delete a transaction',
            'label' => 'Delete a transaction',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/transactions/{transaction}',
            'examplePath' => '/copies/1/transactions/1',
            'description' => 'Delete a transaction.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $transactionId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
