<?php

declare(strict_types=1);

return [
    'name' => 'Health',
    'sections' => [
        [
            'id' => 'health-show',
            'title' => 'Check the API status',
            'label' => 'Check status',
            'method' => 'GET',
            'path' => '/health',
            'auth' => false,
            'description' => 'Check that the API and its underlying services are up. This endpoint requires no authentication and is meant for monitoring, or as a first request when setting up a client.',
            'returns' => 'An object describing the state of each service, or a 500 status when a service is down.',
            'response' => [
                'message' => 'ok',
                'services' => [
                    'database' => 'up',
                ],
            ],
        ],
    ],
];
