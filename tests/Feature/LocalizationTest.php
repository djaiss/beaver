<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

uses(RefreshDatabase::class);

/**
 * @return array<int, string>
 */
function supportedLocales(): array
{
    return config('app.supported_locales');
}

/**
 * @return array<string, string>
 */
function translationsFor(string $locale): array
{
    return json_decode(file_get_contents(lang_path($locale.'.json')), true, 512, JSON_THROW_ON_ERROR);
}

it('ships a translation file for every supported locale', function () {
    foreach (supportedLocales() as $locale) {
        expect(file_exists(lang_path($locale.'.json')))
            ->toBeTrue("lang/{$locale}.json is missing");
    }
});

it('translates every english key in every supported locale', function () {
    $english = translationsFor('en');

    foreach (supportedLocales() as $locale) {
        $translations = translationsFor($locale);

        expect(array_keys($translations))
            ->toEqualCanonicalizing(array_keys($english), "lang/{$locale}.json does not have the same keys as en.json");

        $empty = array_keys(array_filter($translations, fn (string $value): bool => trim($value) === ''));

        expect($empty)->toBeEmpty("lang/{$locale}.json has untranslated keys: ".implode(', ', $empty));
    }
});

it('keeps every placeholder in every supported locale', function () {
    $english = translationsFor('en');

    foreach (supportedLocales() as $locale) {
        if ($locale === 'en') {
            continue;
        }

        $translations = translationsFor($locale);

        foreach ($english as $key => $source) {
            // Placeholders are interpolated at runtime, so dropping or renaming
            // one in a translation silently prints the raw token to the user.
            preg_match_all('/:[a-z_]+/', $source, $expected);
            preg_match_all('/:[a-z_]+/', $translations[$key], $actual);

            expect($actual[0])->toEqualCanonicalizing(
                $expected[0],
                "lang/{$locale}.json has different placeholders for \"{$key}\"",
            );
        }
    }
});

it('accepts every supported locale', function () {
    foreach (supportedLocales() as $locale) {
        $this->from('/')->put('/locale', ['locale' => $locale])->assertRedirect('/');

        expect(App::getLocale())->toEqual($locale);
    }
});

it('rejects a locale that is not supported', function () {
    $this->from('/')
        ->put('/locale', ['locale' => 'xx_XX'])
        ->assertSessionHasErrors('locale');
});
