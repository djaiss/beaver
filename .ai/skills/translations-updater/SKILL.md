---
name: translations-updater
description: Update Laravel PHP translation files by fixing missing or empty translations in lang/**/*.php. Use when UI copy changes, new keys are added, or locale files are out of sync. Trigger whenever translation keys, lang files, i18n, or __() / @lang() strings are mentioned.
---

# Translations Updater

This skill keeps Laravel translation files consistent and complete. It regenerates locale keys using the project command and then fixes missing, empty, or invalid entries inside `lang/**/*.php` files.

## Key conventions

This project uses **short keys** in structured PHP files — not sentence-case strings, not flat JSON files.

```php
// ✅ Correct — lang/en/app/vault.php
return [
    'create' => [
        'title' => 'Create vault',
        'name' => 'Name',
    ],
    'join' => [
        'title' => 'Join vault',
        'invitation_code' => 'Paste the invitation code',
    ],
];
```

```php
// ❌ Wrong — do not use sentence-case flat keys
return [
    "Email address" => "Email address",
];
```

Translations are referenced by file path and key: `__('app/vault.create.title')` for `lang/en/app/vault.php`, or `__('app/settings/settings.details.language')` for nested language files. Never use sentence strings such as `__('Email address')`.

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
3. Add any user-facing locale option labels to the English source language file, such as `lang/en/app/settings/settings.php`.
4. Update UI controls that expose locale choices, such as `resources/views/app/settings/_detail.blade.php`.
5. Run `composer lifeos:locale` so the new `lang/{locale}/**/*.php` tree is created from `lang/en/`.
6. Fill the new locale files completely; do not leave the generated empty strings in place.

### Step 3: Fix translations

1. **Do not remove keys** unless they are confirmed unused.

2. **For empty or invalid values** — replace with an appropriate human-readable translation.

3. **Maintain structural consistency** — nesting depth and key names must match across all locales:
   ```php
   // en/app/auth.php          // fr_FR/app/auth.php
   'login' => [                'login' => [
       'title' => 'Welcome',       'title' => 'Bienvenue',
   ],                          ],
   ```

4. **Preserve placeholders exactly** — do not alter tokens like `:name`, `:count`, `:seconds`, `{value}`.

5. **Preserve embedded HTML or Markdown** — structure must be identical across locales.

6. **Consistency rules:**
   - Same tense and tone as surrounding keys
   - Same terminology used elsewhere in the locale file
   - No informal register unless the file already uses it

### Step 4: Validate PHP correctness

Ensure all modified `lang/{locale}/**/*.php` files:
- Return a valid PHP array (`return [ ... ];`)
- Use `declare(strict_types=1);` if the English source does
- Have no duplicate keys (PHP silently keeps the last one — a common trap)
- Use consistent indentation

### Step 5: Quality checks

1. No empty strings, null values, or `TODO` placeholders remain.
2. If a translation is uncertain, prefer literal and conservative wording.
3. Maintain existing key order unless the project explicitly requires sorting.
4. Run `bash scripts/check-translations.sh` after translation changes; it checks all PHP short-key language files and fails on empty strings.

## Best practices

- Never rename translation keys without corresponding code changes
- Never translate or alter placeholders (`:name`, `:count`, etc.)
- Favor consistency over stylistic variation
- The `shared` sub-key is a code smell — prefer duplicating keys per section rather than creating a cross-cutting `shared` group

## Validation checklist

- [ ] `composer lifeos:locale` executed successfully
- [ ] `config/app.php` and `composer.json` locale lists match when locales change
- [ ] Locale picker UI is updated when a user-selectable locale is added
- [ ] All modified `lang/{locale}/**/*.php` files are valid PHP
- [ ] No missing keys remain (compared to `lang/en/`)
- [ ] No empty, null, or placeholder translation values remain
- [ ] Key structure mirrors the English source exactly
- [ ] `bash scripts/check-translations.sh` passes

## Output expectation

All translation files in `lang/{locale}/**/*.php` are regenerated, complete, valid, and contain no missing or empty translations.
