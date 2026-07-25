<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$result = fn (string $type, string $id, string $title, string $context, ?string $collection, int $score, string $path): array => [
    'type' => $type,
    'id' => $id,
    'title' => $title,
    'context' => $context,
    'collection_name' => $collection,
    'score' => $score,
    'name_match' => $score >= 100,
    'links' => [
        'self' => $base.$path,
    ],
];

return [
    'name' => 'Search',
    'sections' => [
        [
            'id' => 'search-list',
            'title' => 'Search your account',
            'label' => 'Search',
            'method' => 'GET',
            'path' => '/search',
            'examplePath' => '/search?q=spider',
            'description' => 'Search everything your account holds in one call: items, collections, copies, photos, loans, locations, sets, series, categories, tags and documents.',
            'body' => [
                'The query is split into words. Every word has to match somewhere in a record, so adding a word narrows the result rather than widening it. A word matches from its start, which means spi finds Spider-Man, and case and punctuation are ignored, so asm-300, asm 300 and ASM_300 behave the same.',
                'Single character words are never indexed on their own, so they are dropped. A query made only of single characters therefore returns nothing rather than everything.',
                'Names and descriptions are encrypted at rest, so the search runs against an index of keyed hashes rather than the text itself. That index is built as records change; a freshly upgraded instance fills it by running php artisan search:rebuild-index once.',
                'A result is scored by the weakest field its words matched in: 100 when every word appeared in the name of the record, 80 for an identifier or a file name, 60 for something filed around it such as its collection or a tag, and 30 for a description or a note. Results come back ranked, at most 50 of them, and total says how many matched in all. Objects in the trash are not searched.',
                'Every result carries a self link to the screen in the web app that shows it. A tag is only visible to owners and editors, so results of that type are absent for a viewer.',
            ],
            'permissions' => 'Any member of the account. Results never cross accounts.',
            'queryParams' => [
                [
                    'name' => 'q',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The words to search for, up to 255 characters.',
                    'example' => 'spider',
                ],
                [
                    'name' => 'type',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'Narrow the results to one kind of record. One of item, collection, copy, photo, loan, location, set, series, category, tag or document.',
                    'example' => 'item',
                ],
            ],
            'returns' => 'A search_results object.',
            'response' => [
                'data' => [
                    'type' => 'search_results',
                    'attributes' => [
                        'query' => 'spider',
                        'total' => 4,
                        'matched' => 4,
                        'truncated' => false,
                        'counts' => [
                            'item' => 2,
                            'copy' => 1,
                            'tag' => 1,
                        ],
                        'results' => [
                            $result('item', '812', 'Amazing Spider-Man #365', '2 copies', 'Marvel Comics 1990s', 100, '/collections/3/items/812'),
                            $result('item', '640', 'Amazing Spider-Man #300', '1 copy', 'Marvel Comics 1990s', 100, '/collections/3/items/640'),
                            $result('copy', '990', 'ASM-300-B', 'Amazing Spider-Man #300 · Very Fine · Display Case', 'Marvel Comics 1990s', 60, '/collections/3/items/640/copies/990'),
                            $result('tag', '18', 'spider-man', '41 items', null, 100, '/settings/tags'),
                        ],
                    ],
                ],
            ],
        ],
    ],
];
