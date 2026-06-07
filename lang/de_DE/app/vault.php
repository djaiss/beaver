<?php

declare(strict_types=1);

return [
    'create' => [
        'name' => 'Name',
        'name_help' => 'Der Name darf nur Buchstaben, Zahlen, Leerzeichen, Bindestriche und Unterstriche enthalten.',
        'title' => 'Tresor erstellen',
    ],
    'index' => [
        'avatar_alt' => 'Avatar',
        'empty' => 'Du bist noch Mitglied in keinem Tresor.',
        'join' => 'Beitreten',
        'new' => 'Neuer Tresor',
        'title' => 'Dashboard',
        'your_vaults' => 'Deine Tresore',
    ],
    'join' => [
        'invitation_code' => 'Einladungscode einfügen',
        'invitation_code_help' => 'Der Einladungscode wird vom Administrator des Tresors bereitgestellt.',
        'submit' => 'Beitreten',
        'title' => 'Tresor beitreten',
    ],
    'show' => [
        'placeholder' => 'bla',
        'title' => 'Tresor',
    ],
    'adminland' => [
        'destroy_vault' => 'Tresor löschen',
        'destroy_vault_description' => 'Diese Aktion ist unumkehrbar. Alle Tresordaten werden sofort dauerhaft gelöscht.',
        'destroy_vault_30_days' => 'Die Daten selbst bleiben, obwohl sie verschlüsselt sind, 30 Tage lang in unseren Backups, bevor sie dauerhaft gelöscht werden.',
        'destroy_vault_confirm' => 'Bitte sei dir sicher. Diese Aktion kann nicht rückgängig gemacht werden.',
        'edit_title' => 'Tresordetails aktualisieren',
        'vault_name' => 'Name des Tresors',
        'genders' => [
            'confirm_delete' => 'Möchtest du wirklich fortfahren? Diese Aktion kann nicht rückgängig gemacht werden.',
            'count' => ':count Geschlecht(er)',
            'description' => 'Geschlechter werden verwendet, um das Geschlecht einer Person zu identifizieren.',
            'drop_to_move' => 'Hier ablegen, um zu verschieben',
            'edit' => 'Bearbeiten',
            'empty' => 'Erstelle zunächst ein neues Geschlecht.',
            'name' => 'Name des Geschlechts',
            'new' => 'Neues Geschlecht',
            'none' => 'Keine Geschlechter erstellt',
            'title' => 'Alle Geschlechter im Tresor',
        ],
        'marital-statuses' => [
            'confirm_delete' => 'Möchtest du wirklich fortfahren? Dies kann nicht rückgängig gemacht werden.',
            'count' => ':count Familienstand/Familienstände',
            'description' => 'Familienstände werden verwendet, um den Beziehungsstatus einer Person anzugeben.',
            'drop_to_move' => 'Zum Verschieben hier ablegen',
            'edit' => 'Bearbeiten',
            'empty' => 'Erstelle zunächst einen neuen Familienstand.',
            'name' => 'Name des Familienstands',
            'new' => 'Neuer Familienstand',
            'none' => 'Keine Familienstände erstellt',
            'title' => 'Alle Familienstände im Tresor',
        ],
    ],
];
