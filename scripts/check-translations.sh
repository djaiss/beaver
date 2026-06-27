#!/usr/bin/env bash

# Check Translations Script
# Verifies that JSON source-string language files are complete and consistent.

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

echo "Checking translations..."

PROJECT_ROOT="$PROJECT_ROOT" php <<'PHP'
<?php

declare(strict_types=1);

$projectRoot = getenv('PROJECT_ROOT');

if (! is_string($projectRoot) || $projectRoot === '') {
    fwrite(STDERR, "Could not resolve project root.\n");
    exit(1);
}

$langPath = $projectRoot.'/lang';

if (! is_dir($langPath)) {
    fwrite(STDERR, "Language directory not found: {$langPath}\n");
    exit(1);
}

$localeFiles = glob($langPath.'/*.json') ?: [];

if ($localeFiles === []) {
    fwrite(STDERR, "No locale files found under {$langPath}.\n");
    exit(1);
}

$emptyTranslations = [];
$invalidTranslations = [];
$englishTranslations = json_decode((string) file_get_contents($langPath.'/en.json'), true, 512, JSON_THROW_ON_ERROR);
$englishKeys = array_keys($englishTranslations);

foreach ($localeFiles as $localeFile) {
    $locale = pathinfo($localeFile, PATHINFO_FILENAME);
    $translations = json_decode((string) file_get_contents($localeFile), true, 512, JSON_THROW_ON_ERROR);

    if (! is_array($translations)) {
        fwrite(STDERR, "Translation file must contain a JSON object: lang/{$locale}.json\n");
        exit(1);
    }

    if (array_keys($translations) !== $englishKeys) {
        $invalidTranslations[] = "lang/{$locale}.json does not contain the same keys as lang/en.json";
    }

    foreach ($translations as $key => $value) {
        if (! is_string($key) || ! is_string($value)) {
            $invalidTranslations[] = "lang/{$locale}.json contains a non-string key or value";
        } elseif (trim($value) === '') {
            $emptyTranslations[] = "lang/{$locale}.json: {$key}";
        }
    }
}

if ($invalidTranslations !== [] || $emptyTranslations !== []) {
    echo "Found invalid translation strings:\n\n";

    foreach ([...$invalidTranslations, ...$emptyTranslations] as $error) {
        echo "  - {$error}\n";
    }

    echo "\nPlease translate these keys or remove them if they are not needed.\n";
    exit(1);
}

echo "All JSON translations are complete.\n";
echo 'Files checked: '.count($localeFiles)."\n";
echo 'Strings checked: '.(count($englishKeys) * count($localeFiles))."\n";
PHP

echo "Translation check completed successfully."
