<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class LocalizeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'lifeos:localize {locales}';

    /**
     * @var string
     */
    protected $description = 'Synchronize PHP language files from the English master locale';

    public function handle(): int
    {
        $locales = $this->locales();

        if ($locales === []) {
            $this->error('No locales provided.');

            return self::FAILURE;
        }

        $masterFiles = $this->masterLanguageFiles();

        if ($masterFiles === []) {
            $this->error('No English language files found.');

            return self::FAILURE;
        }

        foreach ($locales as $locale) {
            if ($locale === 'en') {
                continue;
            }

            $this->syncLocale($locale, $masterFiles);
        }

        $this->info('Locale files synchronized.');

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function locales(): array
    {
        $localesArgument = (string) $this->argument('locales');

        return array_values(array_filter(array_map(trim(...), explode(',', $localesArgument))));
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function masterLanguageFiles(): array
    {
        $englishPath = lang_path('en');

        if (! is_dir($englishPath)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($englishPath));

        $englishPathPrefix = implode('', [$englishPath, DIRECTORY_SEPARATOR]);

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo) {
                continue;
            }
            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }
            $relativePath = str($fileInfo->getPathname())
                ->after($englishPathPrefix)
                ->replace(DIRECTORY_SEPARATOR, '/')
                ->value();

            $files[$relativePath] = $this->readLanguageFile($fileInfo->getPathname());
        }

        ksort($files);

        return $files;
    }

    /**
     * @return array<string, mixed>
     */
    private function readLanguageFile(string $path): array
    {
        $lines = require $path;

        if (! is_array($lines)) {
            return [];
        }

        return $lines;
    }

    /**
     * @param  array<string, array<string, mixed>>  $masterFiles
     */
    private function syncLocale(string $locale, array $masterFiles): void
    {
        $localePath = lang_path($locale);

        if (! is_dir($localePath)) {
            mkdir($localePath, 0o755, true);
        }

        $this->deleteStaleLanguageFiles($localePath, array_keys($masterFiles));

        foreach ($masterFiles as $relativePath => $masterLines) {
            $targetPath = implode(DIRECTORY_SEPARATOR, [
                $localePath,
                str_replace('/', DIRECTORY_SEPARATOR, $relativePath),
            ]);
            $targetDirectory = dirname($targetPath);

            if (! is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0o755, true);
            }

            $existingLines = is_file($targetPath) ? $this->readLanguageFile($targetPath) : [];
            $syncedLines = $this->syncLines($masterLines, $existingLines);

            file_put_contents($targetPath, $this->renderLanguageFile($syncedLines));
        }
    }

    /**
     * @param  array<int, string>  $masterRelativePaths
     */
    private function deleteStaleLanguageFiles(string $localePath, array $masterRelativePaths): void
    {
        if (! is_dir($localePath)) {
            return;
        }

        $masterRelativePaths = array_flip($masterRelativePaths);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($localePath),
            RecursiveIteratorIterator::CHILD_FIRST,
        );

        $localePathPrefix = implode('', [$localePath, DIRECTORY_SEPARATOR]);

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo) {
                continue;
            }

            if ($fileInfo->isDir()) {
                if (! in_array($fileInfo->getBasename(), ['.', '..'], true)) {
                    $pathname = $fileInfo->getPathname();

                    if (is_dir($pathname) && ! is_link($pathname)) {
                        $isEmpty = count(scandir($pathname)) === 2; // Contains only '.' and '..'

                        if ($isEmpty) {
                            rmdir($pathname);
                        }
                    }
                }

                continue;
            }

            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str($fileInfo->getPathname())
                ->after($localePathPrefix)
                ->replace(DIRECTORY_SEPARATOR, '/')
                ->value();

            if (! Arr::exists($masterRelativePaths, $relativePath)) {
                unlink($fileInfo->getPathname());
            }
        }
    }

    /**
     * @param  array<string, mixed>  $masterLines
     * @param  array<string, mixed>  $existingLines
     * @return array<string, mixed>
     */
    private function syncLines(array $masterLines, array $existingLines): array
    {
        $syncedLines = [];

        foreach ($masterLines as $key => $value) {
            if (is_array($value)) {
                $syncedLines[$key] = $this->syncLines(
                    $value,
                    Arr::exists($existingLines, $key) && is_array($existingLines[$key]) ? $existingLines[$key] : [],
                );

                continue;
            }

            $syncedLines[$key] = Arr::exists($existingLines, $key) && ! is_array($existingLines[$key])
                ? (string) $existingLines[$key]
                : '';
        }

        return $syncedLines;
    }

    /**
     * @param  array<string, mixed>  $lines
     */
    private function renderLanguageFile(array $lines): string
    {
        $renderedArray = $this->renderArray($lines);

        return "<?php\n\ndeclare(strict_types=1);\n\nreturn {$renderedArray};\n";
    }

    /**
     * @param  array<string, mixed>  $lines
     */
    private function renderArray(array $lines, int $level = 0): string
    {
        if ($lines === []) {
            return '[]';
        }

        $indent = str_repeat('    ', $level);
        $childIndent = str_repeat('    ', $level + 1);
        $renderedLines = [];

        foreach ($lines as $key => $value) {
            $renderedValue = is_array($value)
                ? $this->renderArray($value, $level + 1)
                : var_export((string) $value, true);

            $renderedKey = var_export((string) $key, true);
            $renderedLines[] = "{$childIndent}{$renderedKey} => {$renderedValue},";
        }

        $renderedBody = implode("\n", $renderedLines);

        return "[\n{$renderedBody}\n{$indent}]";
    }
}
