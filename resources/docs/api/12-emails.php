<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$email = fn (string $id, string $emailType, string $subject, string $body): array => [
    'type' => 'email',
    'id' => $id,
    'attributes' => [
        'email_type' => $emailType,
        'email_address' => 'monica@example.com',
        'subject' => $subject,
        'body' => $body,
        'sent_at' => 1752537600,
        'delivered_at' => 1752537600,
        'bounced_at' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/administration/emails/'.$id,
    ],
];

return [
    'name' => 'Emails',
    'sections' => [
        [
            'id' => 'emails-list',
            'title' => 'List emails',
            'method' => 'GET',
            'path' => '/administration/emails',
            'description' => 'Retrieve every email the application sent to your user, most recently sent first, with delivery tracking. Emails cannot be sent, changed or deleted through the API.',
            'queryParams' => [
                [
                    'name' => 'per_page',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The number of emails to return per page, between 1 and 100.',
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
            'returns' => 'A paginated list of email objects.',
            'response' => ApiDocumentation::paginated([
                $email('1', 'welcome', 'Welcome to Kollek', 'Thanks for signing up! Here is how to get started.'),
            ], '/administration/emails'),
        ],
        [
            'id' => 'emails-show',
            'title' => 'Get an email',
            'label' => 'Get an email',
            'method' => 'GET',
            'path' => '/administration/emails/{email}',
            'examplePath' => '/administration/emails/1',
            'description' => 'Retrieve a single sent email of your user by its ID.',
            'pathParams' => [
                [
                    'name' => 'email',
                    'type' => 'integer',
                    'required' => true,
                    'description' => 'The ID of the sent email.',
                ],
            ],
            'returns' => 'An email object, or 404 when the email belongs to another user.',
            'response' => ['data' => $email('1', 'welcome', 'Welcome to Kollek', 'Thanks for signing up! Here is how to get started.')],
        ],
    ],
];
