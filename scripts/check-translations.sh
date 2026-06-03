#!/usr/bin/env bash

# Check Translations Script
# Verifies that PHP short-key language files do not contain empty strings.

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

$localePaths = array_values(array_filter(glob($langPath.'/*') ?: [], 'is_dir'));

if ($localePaths === []) {
    fwrite(STDERR, "No locale directories found under {$langPath}.\n");
    exit(1);
}

$emptyTranslations = [];
$checkedFiles = 0;
$checkedStrings = 0;

foreach ($localePaths as $localePath) {
    $locale = basename($localePath);
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($localePath, FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $fileInfo) {
        if (! $fileInfo instanceof SplFileInfo || $fileInfo->getExtension() !== 'php') {
            continue;
        }

        $relativePath = str_replace($localePath.DIRECTORY_SEPARATOR, '', $fileInfo->getPathname());
        $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        $group = substr($relativePath, 0, -4);
        $lines = require $fileInfo->getPathname();

        if (! is_array($lines)) {
            fwrite(STDERR, "Translation file must return an array: lang/{$locale}/{$relativePath}\n");
            exit(1);
        }

        $checkedFiles++;

        $walk = function (array $values, string $prefix = '') use (&$walk, &$emptyTranslations, &$checkedStrings, $locale, $relativePath, $group): void {
            foreach ($values as $key => $value) {
                $keyPath = $prefix === '' ? (string) $key : $prefix.'.'.$key;

                if (is_array($value)) {
                    $walk($value, $keyPath);

                    continue;
                }

                $checkedStrings++;

                if (trim((string) $value) === '') {
                    $emptyTranslations[] = sprintf(
                        'lang/%s/%s:%s.%s',
                        $locale,
                        $relativePath,
                        str_replace('/', '.', $group),
                        $keyPath,
                    );
                }
            }
        };

        $walk($lines);
    }
}

if ($emptyTranslations !== []) {
    echo "Found empty translation strings:\n\n";

    foreach ($emptyTranslations as $emptyTranslation) {
        echo "  - {$emptyTranslation}\n";
    }

    echo "\nPlease translate these keys or remove them if they are not needed.\n";
    exit(1);
}

echo "All PHP translations are complete.\n";
echo "Files checked: {$checkedFiles}\n";
echo "Strings checked: {$checkedStrings}\n";
PHP

echo "Translation check completed successfully."
