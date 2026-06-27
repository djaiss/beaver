---
name: translations-updater
description: Update Laravel PHP translation files by fixing missing or empty translations in lang/**/*.php. Use when UI copy changes, new keys are added, or locale files are out of sync. Trigger whenever translation keys, lang files, i18n, or __() / @lang() strings are mentioned.
---

# Translations Updater

This skill keeps Laravel translation files consistent and complete.
## Key conventions

This project uses **translations keys** in JSON files.

## When to use this Skill

- New UI text or translation keys were introduced
- Modules, pages, or components added new strings
- PHP translation files contain empty or missing values
- Locale files are out of sync with the codebase
- A supported language is added or removed

## Voice and tense per locale for logs

When we are translating logs, match the convention for each language:

| Locale | Convention | Example |
|---|---|---|
| `en` | Simple past, active | "Created an account" |
| `fr_FR` | Passé composé, actif | "A créé un compte" |
| `de` | Perfekt, active (Hat + Partizip) | "Hat ein Konto erstellt" |

## Instructions

### Step 1: Regenerate locale files

1. Run the locale generation command:
   ```bash
   composer lifeos:locale
   ```
2. Confirm the command completes successfully before making any edits.

### Step 2: Add or remove supported locales

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

### Step 3: Fix translations

1. **Do not remove keys** unless they are confirmed unused.

2. **For empty or invalid values** — replace with an appropriate human-readable translation.

4. **Preserve placeholders exactly** — do not alter tokens like `:name`, `:count`, `:seconds`, `{value}`.

5. **Preserve embedded HTML or Markdown** — structure must be identical across locales.

6. **Consistency rules:**
   - Same tense and tone as surrounding keys
   - Same terminology used elsewhere in the locale file
   - No informal register unless the file already uses it

### Step 5: Quality checks

1. No empty strings, null values, or `TODO` placeholders remain.
2. If a translation is uncertain, prefer literal and conservative wording.
3. Maintain existing key order unless the project explicitly requires sorting.
4. Run `bash scripts/check-translations.sh` after translation changes; it checks all PHP short-key language files and fails on empty strings.

## Best practices

- Never rename translation keys without corresponding code changes
- Never translate or alter placeholders (`:name`, `:count`, etc.)
- Favor consistency over stylistic variation
