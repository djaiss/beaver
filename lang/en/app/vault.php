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
        'relationship_types' => [
            'category_name' => 'Name of the relationship type category',
            'confirm_delete_category' => 'Are you sure you want to delete this category and all of its relationship types? This cannot be undone.',
            'confirm_delete_type' => 'Are you sure you want to delete this relationship type? This cannot be undone.',
            'count' => ':count relationship type category(ies)',
            'description' => 'Relationship type categories group the relationship types available when connecting people.',
            'edit_category' => 'Edit category',
            'edit_type' => 'Edit',
            'empty' => 'Get started by creating a relationship type category.',
            'empty_category' => 'No relationship types in this category.',
            'is_directed' => 'This relationship is directed',
            'is_directed_help' => 'Directed relationships can have different meanings depending on which person is viewed first.',
            'new_category' => 'New category',
            'new_type' => 'New relationship type',
            'none' => 'No relationship type categories created',
            'title' => 'Relationship types',
            'type_name' => 'Name of the relationship type',
        ],
    ],
];
