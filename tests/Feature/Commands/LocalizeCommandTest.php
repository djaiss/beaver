<?php

declare(strict_types = 1);

namespace Tests\Feature\Commands;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocalizeCommandTest extends TestCase
{
    #[Test]
    public function it_synchronizes_php_language_files_from_the_english_master_locale(): void
    {
        $paths = [
            lang_path('en/localize-test.php'),
            lang_path('en/localize-test/nested.php'),
            lang_path('fr_FR/localize-test.php'),
            lang_path('fr_FR/localize-test/nested.php'),
            lang_path('fr_FR/localize-test-stale.php'),
        ];

        $originalFiles = [];

        foreach ($paths as $path) {
            $originalFiles[$path] = is_file($path) ? file_get_contents($path) : null;
        }

        try {
            $this->writeLanguageFile(lang_path('en/localize-test.php'), [
                'existing' => 'Existing English value',
                'new' => 'New English value',
                'nested' => [
                    'existing' => 'Existing nested English value',
                    'new' => 'New nested English value',
                ],
            ]);

            $this->writeLanguageFile(lang_path('en/localize-test/nested.php'), [
                'title' => 'Nested file title',
            ]);

            $this->writeLanguageFile(lang_path('fr_FR/localize-test.php'), [
                'existing' => 'Valeur française existante',
                'stale' => 'Supprime-moi',
                'nested' => [
                    'existing' => 'Valeur française imbriquée existante',
                    'stale' => 'Supprime-moi',
                ],
            ]);

            $this->writeLanguageFile(lang_path('fr_FR/localize-test-stale.php'), [
                'title' => 'Supprime-moi',
            ]);

            $this->artisan('lifeos:localize en,fr_FR')
                ->assertSuccessful();

            $this->assertSame(
                [
                    'existing' => 'Valeur française existante',
                    'new' => '',
                    'nested' => [
                        'existing' => 'Valeur française imbriquée existante',
                        'new' => '',
                    ],
                ],
                require lang_path('fr_FR/localize-test.php'),
            );

            $this->assertSame(
                [
                    'title' => '',
                ],
                require lang_path('fr_FR/localize-test/nested.php'),
            );

            $this->assertFileDoesNotExist(lang_path('fr_FR/localize-test-stale.php'));
        } finally {
            foreach ($originalFiles as $path => $contents) {
                if ($contents === null && is_file($path)) {
                    unlink($path);
                }

                if ($contents !== null) {
                    $this->writeRawFile($path, $contents);
                }
            }

            if (is_dir($enDir = lang_path('en/localize-test'))) {
                rmdir($enDir);
            }

            if (is_dir($frDir = lang_path('fr_FR/localize-test'))) {
                rmdir($frDir);
            }
        }
    }

    #[Test]
    public function it_fails_when_no_locales_are_provided(): void
    {
        $this->artisan('lifeos:localize', ['locales' => ''])
            ->assertFailed();
    }

    /**
     * @param  array<string, mixed>  $lines
     */
    private function writeLanguageFile(string $path, array $lines): void
    {
        $exportedLines = var_export($lines, true);

        $this->writeRawFile(
            $path,
            "<?php\n\ndeclare(strict_types=1);\n\nreturn {$exportedLines};\n",
        );
    }

    private function writeRawFile(string $path, string $contents): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0o755, true);
        }

        file_put_contents($path, $contents);
    }
}
