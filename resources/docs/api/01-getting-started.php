<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

return [
    'name' => 'Getting started',
    'guide' => true,
    'sections' => [
        [
            'id' => 'introduction',
            'kicker' => 'OVERVIEW',
            'title' => 'Introduction',
            'description' => 'The '.config('app.name').' API gives you programmatic access to everything in your account: collections, collection types, custom fields and locations. The API is organized around REST, returns JSON on every endpoint, and loosely follows the JSON:API specification for response shapes.',
            'body' => [
                'There is no test mode. Every request is processed against your production account, so be careful with destructive calls.',
                'The API does not support bulk updates. Work on one object per request.',
            ],
            'method' => 'GET',
            'path' => '/health',
            'auth' => false,
            'response' => [
                'message' => 'ok',
                'services' => [
                    'database' => 'up',
                ],
            ],
        ],
        [
            'id' => 'authentication-guide',
            'label' => 'Authentication',
            'kicker' => 'AUTH',
            'title' => 'Authentication',
            'description' => 'Authenticate every request with a bearer token in the Authorization header. Get a token by registering or logging in through the API, or create one from your profile settings, under API keys.',
            'body' => [
                'Tokens do not expire, but you can revoke them at any time from your profile or through the API keys endpoints.',
                'If two-factor authentication is enabled on your user, the login endpoint also requires a valid TOTP or recovery code.',
            ],
            'method' => 'GET',
            'path' => '/me',
            'response' => [
                'data' => [
                    'type' => 'user',
                    'id' => '1',
                    'attributes' => [
                        'first_name' => 'Monica',
                        'last_name' => 'Geller',
                        'nickname' => 'Mon',
                        'email' => 'monica@example.com',
                        'locale' => 'en',
                        'time_format_24h' => true,
                    ],
                    'links' => [
                        'self' => $base.'/me',
                    ],
                ],
            ],
        ],
        [
            'id' => 'rate-limits',
            'label' => 'Rate limits',
            'kicker' => 'LIMITS',
            'title' => 'Rate limits',
            'description' => 'Authenticated endpoints are limited to 60 requests per minute per user. The register and login endpoints are limited to 6 requests per minute. Requests over the limit return 429 with a Retry-After header telling you how many seconds to wait.',
            'method' => 'GET',
            'path' => '/collections',
            'responseStatus' => 429,
            'response' => [
                'message' => 'Too Many Attempts.',
            ],
        ],
        [
            'id' => 'pagination',
            'kicker' => 'PAGINATION',
            'title' => 'Pagination',
            'description' => 'Every list endpoint is paginated. Pass per_page to control the page size (between 1 and 100) and page to move through the results. Responses include links and meta objects to navigate between pages.',
            'method' => 'GET',
            'path' => '/collections',
            'examplePath' => '/collections?per_page=1&page=2',
            'queryParams' => [
                [
                    'name' => 'per_page',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The number of objects to return per page, between 1 and 100.',
                    'default' => '10',
                ],
                [
                    'name' => 'page',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The page number to return.',
                    'default' => '1',
                ],
            ],
            'response' => [
                'data' => [
                    [
                        'type' => 'collection',
                        'id' => '2',
                        'attributes' => [
                            'uuid' => '9e2f6c1a-4b3d-4b6e-9a2f-0c1d2e3f4a5b',
                            'name' => 'Vinyl',
                            'description' => null,
                            'emoji' => '💿',
                            'visibility' => 'shared',
                            'currency' => 'USD',
                            'created_at' => 1752537600,
                            'updated_at' => 1752537600,
                        ],
                        'links' => [
                            'self' => $base.'/collections/2',
                        ],
                    ],
                ],
                'links' => [
                    'first' => $base.'/collections?page=1',
                    'last' => $base.'/collections?page=2',
                    'prev' => $base.'/collections?page=1',
                    'next' => null,
                ],
                'meta' => [
                    'current_page' => 2,
                    'from' => 2,
                    'last_page' => 2,
                    'links' => [
                        ['url' => $base.'/collections?page=1', 'label' => '&laquo; Previous', 'active' => false],
                        ['url' => $base.'/collections?page=1', 'label' => '1', 'active' => false],
                        ['url' => $base.'/collections?page=2', 'label' => '2', 'active' => true],
                        ['url' => null, 'label' => 'Next &raquo;', 'active' => false],
                    ],
                    'path' => $base.'/collections',
                    'per_page' => 1,
                    'to' => 2,
                    'total' => 2,
                ],
            ],
        ],
        [
            'id' => 'errors',
            'kicker' => 'ERRORS',
            'title' => 'Errors',
            'description' => 'The API uses conventional HTTP status codes: 2xx for success, 4xx for request errors, 5xx for server errors. Requests for an object that does not exist in your account return 404, and so do write requests made with a role that does not allow them.',
            'body' => [
                'Validation failures return 422 with a message and an errors object keyed by field name. Authentication failures return 401 with a message.',
            ],
            'method' => 'POST',
            'path' => '/collections',
            'exampleBody' => [
                'visibility' => 'private',
            ],
            'responseStatus' => 422,
            'response' => [
                'message' => 'The name field is required.',
                'errors' => [
                    'name' => [
                        'The name field is required.',
                    ],
                ],
            ],
        ],
    ],
];
