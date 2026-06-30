---
name: add-locale
description: Add a new locale to the application. Use when the user wants to support an additional language, register a locale in config, and generate its lang/*.json translation file. Trigger whenever adding a language, new locale, or i18n support is mentioned.
---

### Add a new locale

When adding a new locale, use ISO 15897 names for region-specific languages, e.g. `fr_FR` for French from France and `es_ES` for Spanish from Spain.

1. Update `config/app.php`:
   ```php
   'supported_locales' => ['en', 'fr_FR', 'es_ES'],
   ```
2. Update the `composer.json` `lifeos:locale` script with the same locale list:
   ```json
   "lifeos:locale": "php artisan lifeos:localize en,fr_FR,es_ES"
   ```
3. Add any user-facing locale option labels to the English source language file.
4. Update UI controls that expose locale choices, such as `resources/views/app/settings/_detail.blade.php`.
5. Run `composer lifeos:locale`.
6. Fill the new locale files completely; do not leave the generated empty strings in place.
