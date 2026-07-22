<?php

declare(strict_types=1);

it('synchronizes json locale files', function () {
    $viewPath = resource_path('views/test-localize-command.blade.php');
    $enPath = lang_path('en.json');
    $frPath = lang_path('fr_FR.json');

    $originalView = is_file($viewPath) ? file_get_contents($viewPath) : null;
    $originalEn = file_get_contents($enPath);
    $originalFr = file_get_contents($frPath);

    try {
        file_put_contents(
            $viewPath,
            <<<'BLADE'
{{ __('Localize test key') }}
{{ trans("Localize double key") }}
@lang('Localize lang key')
{{ trans_key('Localize custom key') }}
{{ __('We\'ve sent you a temporary login link. This link is valid for 5 minutes. Please check your inbox.') }}
BLADE
        );

        file_put_contents(
            $enPath,
            json_encode([
                'Localize test key' => 'Preserved English Value',
                'Localize stale key' => 'Remove me',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        file_put_contents(
            $frPath,
            json_encode([
                'Localize test key' => 'Valeur Française Conservée',
                'Localize stale key' => 'Supprime-moi',
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL,
        );

        $this->artisan('kollek:localize en,fr_FR')
            ->assertSuccessful();

        $enTranslations = json_decode((string) file_get_contents($enPath), true);
        $frTranslations = json_decode((string) file_get_contents($frPath), true);

        expect($enTranslations)->toBeArray();
        expect($frTranslations)->toBeArray();

        expect($enTranslations['Localize test key'])->toBe('Preserved English Value');
        expect($frTranslations['Localize test key'])->toBe('Valeur Française Conservée');

        expect($enTranslations['Localize double key'])->toBe('Localize double key');
        expect($enTranslations['Localize lang key'])->toBe('Localize lang key');
        expect($enTranslations['Localize custom key'])->toBe('Localize custom key');
        expect($enTranslations['We\'ve sent you a temporary login link. This link is valid for 5 minutes. Please check your inbox.'])->toBe('We\'ve sent you a temporary login link. This link is valid for 5 minutes. Please check your inbox.');

        expect($frTranslations['Localize double key'])->toBe('');
        expect($frTranslations['Localize lang key'])->toBe('');
        expect($frTranslations['Localize custom key'])->toBe('');
        expect($frTranslations['We\'ve sent you a temporary login link. This link is valid for 5 minutes. Please check your inbox.'])->toBe('');
        $this->assertArrayNotHasKey('We\\', $frTranslations);

        $this->assertArrayNotHasKey('Localize stale key', $enTranslations);
        $this->assertArrayNotHasKey('Localize stale key', $frTranslations);
    } finally {
        if ($originalView === null) {
            @unlink($viewPath);
        } else {
            file_put_contents($viewPath, $originalView);
        }

        file_put_contents($enPath, (string) $originalEn);
        file_put_contents($frPath, (string) $originalFr);
    }
});
