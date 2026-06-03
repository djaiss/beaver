<?php

declare(strict_types=1);

return [
    'api' => [
        'confirm_delete' => 'Êtes-vous sûr de vouloir continuer ? Cette action est irréversible.',
        'copied' => 'Copié',
        'copy' => 'Copier',
        'count' => ':count clé(s) API créée(s)',
        'create_success' => 'Clé API créée avec succès',
        'description' => 'Les clés API sont comme des mots de passe secrets qui permettent à d’autres outils ou applications de se connecter à votre compte en toute sécurité.',
        'empty' => 'Aucune clé API créée',
        'label' => 'Libellé de la clé API',
        'new' => 'Nouvelle clé API',
        'private_description' => 'Chaque clé API vous est propre. Traitez-les comme des mots de passe privés : ne les partagez avec personne en qui vous n’avez pas confiance.',
        'title' => 'Clés API personnelles',
        'warning' => 'Veuillez copier votre clé API maintenant. Pour des raisons de sécurité, elle ne sera plus affichée.',
    ],
    'auto_delete' => [
        'description' => 'Supprimer automatiquement le compte et toutes les données après 6 mois d’inactivité. Veuillez en être certain.',
        'label' => 'Supprimer mon compte après 6 mois d’inactivité',
        'title' => 'Suppression automatique du compte',
    ],
    'password' => [
        'confirm' => 'Confirmer le nouveau mot de passe',
        'current' => 'Mot de passe actuel',
        'minimum' => 'Minimum 8 caractères.',
        'new' => 'Nouveau mot de passe',
        'title' => 'Changer le mot de passe',
    ],
    'two_factor' => [
        'authenticator_app' => 'Application d’authentification',
        'code_description' => 'Entrez ci-dessous le code généré par votre application d’authentification.',
        'configured' => 'Configuré',
        'confirm_remove' => 'Êtes-vous absolument sûr ? Cette action est irréversible.',
        'description' => 'Utilisez une application d’authentification pour obtenir des codes d’authentification à deux facteurs lorsque cela vous est demandé.',
        'recovery_codes_description' => 'Utilisez ces codes pour accéder à votre compte si vous perdez l’accès à votre application d’authentification.',
        'recovery_codes' => 'Codes de récupération',
        'scan_description' => 'Utilisez n’importe quelle application d’authentification pour scanner votre code QR, ou utilisez manuellement la clé de configuration.',
        'set_up' => 'Configurer',
        'setup_key' => 'Clé de configuration :',
        'token_label' => 'Entrez le jeton OTP à 6 chiffres',
    ],
    'title' => 'Sécurité et accès',
];
