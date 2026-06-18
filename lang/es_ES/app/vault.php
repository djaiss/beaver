<?php

declare(strict_types=1);

return [
    'create' => [
        'name' => 'Nombre',
        'name_help' => 'El nombre solo puede contener letras, números, espacios, guiones y guiones bajos.',
        'title' => 'Crear bóveda',
    ],
    'index' => [
        'avatar_alt' => 'Avatar',
        'empty' => 'Todavía no eres miembro de ninguna bóveda.',
        'join' => 'Unirse',
        'new' => 'Nueva bóveda',
        'title' => 'Panel',
        'your_vaults' => 'Tus bóvedas',
    ],
    'join' => [
        'invitation_code' => 'Pega el código de invitación',
        'invitation_code_help' => 'El código de invitación lo proporciona el administrador de la bóveda.',
        'submit' => 'Unirse',
        'title' => 'Unirse a una bóveda',
    ],
    'show' => [
        'placeholder' => 'bla',
        'title' => 'Bóveda',
    ],
    'adminland' => [
        'destroy_vault' => 'Destruir bóveda',
        'destroy_vault_description' => 'Esta acción es irreversible. Todos los datos de la bóveda se eliminarán permanentemente de inmediato.',
        'destroy_vault_30_days' => 'Los datos en sí, aunque están cifrados, permanecerán en nuestras copias de seguridad durante 30 días antes de eliminarse permanentemente.',
        'destroy_vault_confirm' => 'Asegúrate bien. Esta acción no se puede deshacer.',
        'edit_title' => 'Actualizar detalles de la bóveda',
        'vault_name' => 'Nombre de la bóveda',
        'genders' => [
            'confirm_delete' => '¿Seguro que quieres continuar? Esta acción no se puede deshacer.',
            'count' => ':count género(s)',
            'description' => 'Los géneros se usan para identificar el género de una persona.',
            'drop_to_move' => 'Suelta aquí para mover',
            'edit' => 'Editar',
            'empty' => 'Empieza creando un nuevo género.',
            'name' => 'Nombre del género',
            'new' => 'Nuevo género',
            'none' => 'No se han creado géneros',
            'title' => 'Todos los géneros de la bóveda',
        ],
        'relationship_types' => [
            'category_name' => 'Nombre de la categoría de tipos de relación',
            'confirm_delete_category' => '¿Seguro que quieres eliminar esta categoría y todos sus tipos de relación? Esta acción no se puede deshacer.',
            'confirm_delete_type' => '¿Seguro que quieres eliminar este tipo de relación? Esta acción no se puede deshacer.',
            'count' => ':count categoría(s) de tipos de relación',
            'description' => 'Las categorías de tipos de relación agrupan los tipos disponibles al conectar personas.',
            'edit_category' => 'Editar categoría',
            'edit_type' => 'Editar',
            'empty' => 'Empieza creando una categoría de tipos de relación.',
            'empty_category' => 'No hay tipos de relación en esta categoría.',
            'is_directed' => 'Esta relación es dirigida',
            'is_directed_help' => 'Las relaciones dirigidas pueden tener significados diferentes según la persona que se consulte primero.',
            'new_category' => 'Nueva categoría',
            'new_type' => 'Nuevo tipo de relación',
            'none' => 'No se han creado categorías de tipos de relación',
            'title' => 'Tipos de relación',
            'type_name' => 'Nombre del tipo de relación',
        ],
    ],
];
