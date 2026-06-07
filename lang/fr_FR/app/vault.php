<?php

declare(strict_types=1);

return [
    'create' => [
        'name' => 'Nom',
        'name_help' => 'Le nom ne peut contenir que des lettres, des chiffres, des espaces, des traits d’union et des traits de soulignement.',
        'title' => 'Créer un coffre',
    ],
    'index' => [
        'avatar_alt' => 'Avatar',
        'empty' => 'Vous n’êtes encore membre d’aucun coffre.',
        'join' => 'Rejoindre',
        'new' => 'Nouveau coffre',
        'title' => 'Tableau de bord',
        'your_vaults' => 'Vos coffres',
    ],
    'join' => [
        'invitation_code' => 'Collez le code d’invitation',
        'invitation_code_help' => 'Le code d’invitation est fourni par l’administrateur du coffre.',
        'submit' => 'Rejoindre',
        'title' => 'Rejoindre un coffre',
    ],
    'show' => [
        'placeholder' => 'bla',
        'title' => 'Coffre',
    ],
    'adminland' => [
        'destroy_vault' => 'Détruire le coffre',
        'destroy_vault_description' => 'Cette action est irréversible. Toutes les données du coffre seront supprimées définitivement et immédiatement.',
        'destroy_vault_30_days' => 'Les données elles-mêmes, bien que chiffrées, resteront dans nos sauvegardes pendant 30 jours avant d’être supprimées définitivement.',
        'destroy_vault_confirm' => 'Veuillez en être certain. Cette action est irréversible.',
        'edit_title' => 'Mettre à jour les détails du coffre',
        'vault_name' => 'Nom du coffre',
        'genders' => [
            'confirm_delete' => 'Voulez-vous vraiment continuer ? Cette action est irréversible.',
            'count' => ':count genre(s)',
            'description' => 'Les genres servent à identifier le genre d’une personne.',
            'drop_to_move' => 'Déposez ici pour déplacer',
            'edit' => 'Modifier',
            'empty' => 'Commencez par créer un nouveau genre.',
            'name' => 'Nom du genre',
            'new' => 'Nouveau genre',
            'none' => 'Aucun genre créé',
            'title' => 'Tous les genres du coffre',
        ],
        'marital-statuses' => [
            'confirm_delete' => 'Êtes-vous sûr de vouloir continuer ? Cette action est irréversible.',
            'count' => ':count statut(s) marital(aux)',
            'description' => 'Les statuts maritaux servent à identifier la situation relationnelle d’une personne.',
            'drop_to_move' => 'Déposer ici pour déplacer',
            'edit' => 'Modifier',
            'empty' => 'Commencez par créer un nouveau statut marital.',
            'name' => 'Nom du statut marital',
            'new' => 'Nouveau statut marital',
            'none' => 'Aucun statut marital créé',
            'title' => 'Tous les statuts maritaux du coffre',
        ],
    ],
];
