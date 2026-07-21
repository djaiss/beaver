<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Documentation portal source
    |--------------------------------------------------------------------------
    |
    | The directory that holds the Markdown files powering the product
    | documentation portal. Each direct subdirectory is a locale (en, fr_FR).
    |
    */

    'portal_path' => base_path('docs/portal'),

    /*
    |--------------------------------------------------------------------------
    | Default locale
    |--------------------------------------------------------------------------
    |
    | The locale served when none is given, and the one every other locale
    | falls back to when a page has not been translated yet.
    |
    */

    'default_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | The locales offered in the language selector, each with the label and
    | flag shown in the picker. A locale only appears once its folder exists.
    |
    */

    'locales' => [
        'en' => ['code' => 'EN', 'label' => 'English', 'flag' => '🇬🇧'],
        'fr_FR' => ['code' => 'FR', 'label' => 'Français', 'flag' => '🇫🇷'],
        'es_ES' => ['code' => 'ES', 'label' => 'Español', 'flag' => '🇪🇸'],
        'de_DE' => ['code' => 'DE', 'label' => 'Deutsch', 'flag' => '🇩🇪'],
        'pt_BR' => ['code' => 'PT', 'label' => 'Português', 'flag' => '🇧🇷'],
        'zh_CN' => ['code' => 'ZH', 'label' => '简体中文', 'flag' => '🇨🇳'],
        'ja_JP' => ['code' => 'JA', 'label' => '日本語', 'flag' => '🇯🇵'],
    ],

];
