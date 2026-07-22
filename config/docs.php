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
    | Help snippets source
    |--------------------------------------------------------------------------
    |
    | The directory that holds the short Markdown snippets shown in the "?"
    | help popovers across the app. Each direct subdirectory is a locale, and
    | each file is one snippet, keyed by the id in its frontmatter.
    |
    */

    'help_path' => base_path('docs/help'),

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
    | "url" is the short language prefix used at the root of the documentation
    | URL (getkollek.com/{url}/...), kept distinct from the locale key itself
    | (fr_FR) since the URL follows the two letter convention readers expect.
    |
    */

    'locales' => [
        'en' => ['url' => 'en', 'code' => 'EN', 'label' => 'English', 'flag' => '🇬🇧'],
        'fr_FR' => ['url' => 'fr', 'code' => 'FR', 'label' => 'Français', 'flag' => '🇫🇷'],
        'es_ES' => ['url' => 'es', 'code' => 'ES', 'label' => 'Español', 'flag' => '🇪🇸'],
        'de_DE' => ['url' => 'de', 'code' => 'DE', 'label' => 'Deutsch', 'flag' => '🇩🇪'],
        'pt_BR' => ['url' => 'pt', 'code' => 'PT', 'label' => 'Português', 'flag' => '🇧🇷'],
        'zh_CN' => ['url' => 'zh', 'code' => 'ZH', 'label' => '简体中文', 'flag' => '🇨🇳'],
        'ja_JP' => ['url' => 'ja', 'code' => 'JA', 'label' => '日本語', 'flag' => '🇯🇵'],
    ],

];
