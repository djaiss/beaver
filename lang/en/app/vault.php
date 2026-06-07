<?php

declare(strict_types=1);

return [
    'create' => [
        'name' => 'Name',
        'name_help' => 'The name can only contain letters, numbers, spaces, hyphens, and underscores.',
        'title' => 'Create vault',
    ],
    'index' => [
        'avatar_alt' => 'Avatar',
        'empty' => 'You are not a member of any vaults yet.',
        'join' => 'Join',
        'new' => 'New vault',
        'title' => 'Dashboard',
        'your_vaults' => 'Your vaults',
    ],
    'join' => [
        'invitation_code' => 'Paste the invitation code',
        'invitation_code_help' => 'The invitation code is given by the vault administrator.',
        'submit' => 'Join',
        'title' => 'Join vault',
    ],
    'show' => [
        'placeholder' => 'bla',
        'title' => 'Vault',
    ],
    'adminland' => [
        'destroy_vault' => 'Destroy vault',
        'destroy_vault_description' => 'This action is irreversible. All vault data will be permanently deleted immediately.',
        'destroy_vault_30_days' => 'The data itself, while encrypted, will remain in our backups for 30 days before being permanently deleted.',
        'destroy_vault_confirm' => 'Please be certain. This action cannot be undone.',
        'edit_title' => 'Update vault details',
        'vault_name' => 'Name of the vault',
        'genders' => [
            'confirm_delete' => 'Are you sure you want to proceed? This cannot be undone.',
            'count' => ':count gender(s)',
            'description' => 'Genders are used to identify the gender of a person.',
            'drop_to_move' => 'Drop here to move',
            'edit' => 'Edit',
            'empty' => 'Get started by creating a new gender.',
            'name' => 'Name of the gender',
            'new' => 'New gender',
            'none' => 'No genders created',
            'title' => 'All the genders in the vault',
        ],
        'marital-statuses' => [
            'confirm_delete' => 'Are you sure you want to proceed? This cannot be undone.',
            'count' => ':count marital status(es)',
            'description' => 'Marital statuses are used to identify the relationship status of a person.',
            'drop_to_move' => 'Drop here to move',
            'edit' => 'Edit',
            'empty' => 'Get started by creating a new marital status.',
            'name' => 'Name of the marital status',
            'new' => 'New marital status',
            'none' => 'No marital statuses created',
            'title' => 'All the marital statuses in the vault',
        ],
    ],
];
