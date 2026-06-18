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
        'relationship_types' => [
            'category_name' => 'Nom de la catégorie de types de relation',
            'confirm_delete_category' => 'Voulez-vous vraiment supprimer cette catégorie et tous ses types de relation ? Cette action est irréversible.',
            'confirm_delete_type' => 'Voulez-vous vraiment supprimer ce type de relation ? Cette action est irréversible.',
            'count' => ':count catégorie(s) de types de relation',
            'description' => 'Les catégories de types de relation regroupent les types disponibles pour relier des personnes.',
            'edit_category' => 'Modifier la catégorie',
            'edit_type' => 'Modifier',
            'empty' => 'Commencez par créer une catégorie de types de relation.',
            'empty_category' => 'Aucun type de relation dans cette catégorie.',
            'is_directed' => 'Cette relation est orientée',
            'is_directed_help' => 'Les relations orientées peuvent avoir une signification différente selon la personne consultée en premier.',
            'new_category' => 'Nouvelle catégorie',
            'new_type' => 'Nouveau type de relation',
            'none' => 'Aucune catégorie de types de relation créée',
            'title' => 'Types de relation',
            'type_name' => 'Nom du type de relation',
        ],
    ],
];
