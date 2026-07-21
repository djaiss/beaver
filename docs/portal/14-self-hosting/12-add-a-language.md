---
id: selfHosting.addLanguage
title: Add a language
slug: add-a-language
section: self-hosting
---

# Add a language

KolleK ships in seven languages: English, French, Spanish, German, Brazilian Portuguese, Simplified Chinese, and Japanese. Each user picks their own language from their profile, and can even switch it from the sign in page. This page explains how translations work under the hood, and how an operator or contributor adds a new locale or completes an existing one.

If you only want to change the language you see, you do not need any of this. See [Change your language](../9-account-and-profile/4-change-your-language.md).

## How translations are stored

Every locale is one JSON file under `lang/`, named after the locale code, for example `lang/fr_FR.json`. Each file maps the original English string to its translation. The list of locales the app offers is defined in the application configuration as the supported locales.

## Scaffold or refresh a locale

The `beaver:localize` command scans the whole application for translatable strings and syncs them into a locale's file:

```
php artisan beaver:localize fr_FR
```

Strings that are new since the last run are added, and strings that no longer exist are removed. In the English file every string is its own translation, so English is always complete by definition. In every other locale, new strings arrive empty, ready for a translator to fill in.

Adding a brand new language is the same flow: register the locale in the supported locales configuration, run the command with the new locale code to generate its file, then translate the empty entries.

:::note
An empty translation falls back to English rather than breaking the interface, so a partially translated locale is usable while work continues.
:::

## What is not translated yet

The logged in application is fully translatable. The public marketing site and the generated API reference are not translated yet and always render in English, whatever locale a visitor uses. See [Feature status and roadmap](../15-troubleshooting/5-feature-status.md).

## Where to next

- Run the command on your instance with [Administer with the command line](11-cli-commands.md).
- See the reader's side of this in [Change your language](../9-account-and-profile/4-change-your-language.md).
