<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$account = fn (string $id, string $name, string $currency): array => [
    'type' => 'account',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'currency_code' => $currency,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/account',
    ],
];

$member = fn (string $id, string $firstName, string $lastName, string $email, string $role): array => [
    'type' => 'member',
    'id' => $id,
    'attributes' => [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'nickname' => null,
        'email' => $email,
        'role' => $role,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/account/members/'.$id,
    ],
];

$invitation = fn (string $id, string $email, string $role): array => [
    'type' => 'invitation',
    'id' => $id,
    'attributes' => [
        'email' => $email,
        'role' => $role,
        'expires_at' => 1753142400,
        'accepted_at' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/account/invitations',
    ],
];

$memberId = [
    'name' => 'member',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the member.',
];

$pagination = fn (string $noun): array => [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of '.$noun.' to return per page, between 1 and 100.',
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

return [
    'name' => 'Account',
    'sections' => [
        [
            'id' => 'account-show',
            'title' => 'Get the account',
            'label' => 'Get the account',
            'method' => 'GET',
            'path' => '/account',
            'description' => 'Retrieve the account your API key belongs to. A user belongs to exactly one account, so there is no ID to pass.',
            'permissions' => 'Any member of the account.',
            'returns' => 'An account object.',
            'response' => ['data' => $account('1', 'Central Perk', 'USD')],
        ],
        [
            'id' => 'account-update',
            'title' => 'Update the account',
            'label' => 'Update the account',
            'method' => 'PUT',
            'path' => '/account',
            'description' => 'Update the name and default currency of the account. The currency applies to new amounts. Amounts already recorded keep the currency they were entered in.',
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the account. Maximum 100 characters.',
                    'example' => 'Central Perk',
                ],
                [
                    'name' => 'currency_code',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The three letter code of the default currency, such as USD or EUR.',
                    'example' => 'USD',
                ],
            ],
            'returns' => 'The updated account object.',
            'response' => ['data' => $account('1', 'Central Perk', 'USD')],
        ],
        [
            'id' => 'account-destroy',
            'title' => 'Delete the account',
            'label' => 'Delete the account',
            'method' => 'DELETE',
            'path' => '/account',
            'description' => 'Permanently delete the account and everything in it: every collection, item, copy, photo and type, along with every member, including you.',
            'body' => [
                'This cannot be undone and there is no confirmation step. The API key used to make the call stops working immediately, as do the keys of every other member. Nothing goes to the trash, so an emptied account cannot be recovered from it.',
            ],
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
        [
            'id' => 'account-members-list',
            'title' => 'List members',
            'label' => 'List members',
            'method' => 'GET',
            'path' => '/account/members',
            'description' => 'Retrieve the people who have access to the account, together with the role each one holds.',
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'queryParams' => $pagination('members'),
            'returns' => 'A paginated list of member objects.',
            'response' => ApiDocumentation::paginated([
                $member('1', 'Rachel', 'Green', 'rachel@centralperk.test', 'owner'),
                $member('2', 'Chandler', 'Bing', 'chandler@centralperk.test', 'editor'),
            ], '/account/members'),
        ],
        [
            'id' => 'account-members-show',
            'title' => 'Get a member',
            'label' => 'Get a member',
            'method' => 'GET',
            'path' => '/account/members/{member}',
            'examplePath' => '/account/members/2',
            'description' => 'Retrieve a single member of your account by their ID.',
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'pathParams' => [$memberId],
            'returns' => 'A member object, or 404 when the member does not belong to your account.',
            'response' => ['data' => $member('2', 'Chandler', 'Bing', 'chandler@centralperk.test', 'editor')],
        ],
        [
            'id' => 'account-members-create',
            'title' => 'Invite a member',
            'label' => 'Invite a member',
            'method' => 'POST',
            'path' => '/account/members',
            'description' => 'Invite someone to join the account at a given role. This creates an invitation and emails it. It does not create a member.',
            'body' => [
                'The invitation is claimed on the web, so the person becomes a member only once they follow the link and set up their user. Until then they show up under the invitations endpoint rather than the members one. The API cannot claim an invitation.',
            ],
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'email',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The email address to invite, in lowercase. Maximum 255 characters.',
                    'example' => 'phoebe@centralperk.test',
                ],
                [
                    'name' => 'role',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The role to grant. One of owner, editor or viewer.',
                    'example' => 'editor',
                ],
            ],
            'returns' => 'The created invitation object.',
            'responseStatus' => 201,
            'response' => ['data' => $invitation('1', 'phoebe@centralperk.test', 'editor')],
        ],
        [
            'id' => 'account-members-update',
            'title' => 'Change the role of a member',
            'label' => 'Change a role',
            'method' => 'PUT',
            'path' => '/account/members/{member}',
            'examplePath' => '/account/members/2',
            'description' => 'Change the role a member holds in the account.',
            'body' => [
                'An account always keeps at least one owner, so demoting the last one is refused.',
            ],
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'pathParams' => [$memberId],
            'bodyParams' => [
                [
                    'name' => 'role',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The role to grant. One of owner, editor or viewer.',
                    'example' => 'viewer',
                ],
            ],
            'returns' => 'The updated member object.',
            'response' => ['data' => $member('2', 'Chandler', 'Bing', 'chandler@centralperk.test', 'viewer')],
        ],
        [
            'id' => 'account-members-destroy',
            'title' => 'Remove a member',
            'label' => 'Remove a member',
            'method' => 'DELETE',
            'path' => '/account/members/{member}',
            'examplePath' => '/account/members/2',
            'description' => 'Remove someone from the account. Their user is deleted and their API keys stop working. What they catalogued stays in the account.',
            'body' => [
                'An account always keeps at least one owner, so removing the last one is refused.',
            ],
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'pathParams' => [$memberId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
        [
            'id' => 'account-invitations-list',
            'title' => 'List pending invitations',
            'label' => 'List invitations',
            'method' => 'GET',
            'path' => '/account/invitations',
            'description' => 'Retrieve the invitations that are still waiting to be claimed. Invitations that were accepted, and those that have expired, are left out.',
            'permissions' => 'Owners only. Editors and viewers get a 404 response.',
            'queryParams' => $pagination('invitations'),
            'returns' => 'A paginated list of invitation objects.',
            'response' => ApiDocumentation::paginated([
                $invitation('1', 'phoebe@centralperk.test', 'editor'),
            ], '/account/invitations'),
        ],
    ],
];
