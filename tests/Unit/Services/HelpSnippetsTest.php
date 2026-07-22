<?php

declare(strict_types=1);

use App\Services\DocumentationPortal;
use App\Services\HelpSnippets;
use Illuminate\Support\Facades\File;

it('resolves a known snippet with its kicker, title, body, note and documentation url', function () {
    $snippet = app(HelpSnippets::class)->find('settings.general.currency', 'en');

    expect($snippet)->not->toBeNull();
    expect($snippet['kicker'])->toBe('Valuation');
    expect($snippet['title'])->toBe('Default currency');
    expect($snippet['body'])->toContain('valuation totals');
    expect($snippet['url'])->toContain('account-settings');
    expect($snippet['note'])->not->toBeNull();
    expect($snippet['note']['title'])->toBe('Not a conversion');
    expect($snippet['note']['text'])->toContain('does not convert');
});

it('resolves the account name snippet, which declares no note', function () {
    $snippet = app(HelpSnippets::class)->find('settings.general.account_name', 'en');

    expect($snippet)->not->toBeNull();
    expect($snippet['title'])->toBe('Account name');
    expect($snippet['note'])->toBeNull();
    expect($snippet['url'])->toContain('account-settings');
});

it('leaves the note null when a snippet declares none', function () {
    $path = storage_path('framework/testing/help/en');
    Illuminate\Support\Facades\File::ensureDirectoryExists($path);
    Illuminate\Support\Facades\File::put($path.'/no.note.md', "---\nid: no.note\ntitle: No note here\n---\nA blurb with no callout.");

    config(['docs.help_path' => storage_path('framework/testing/help')]);

    $snippet = (new HelpSnippets(app(DocumentationPortal::class)))->find('no.note', 'en');

    Illuminate\Support\Facades\File::deleteDirectory(storage_path('framework/testing/help'));

    expect($snippet)->not->toBeNull();
    expect($snippet['note'])->toBeNull();
    expect($snippet['kicker'])->toBeNull();
});

it('returns null for an unknown id', function () {
    $snippet = app(HelpSnippets::class)->find('nope.nope', 'en');

    expect($snippet)->toBeNull();
});

it('serves the translated snippet when the locale has one', function () {
    $snippet = app(HelpSnippets::class)->find('settings.general.currency', 'fr_FR');

    expect($snippet)->not->toBeNull();
    expect($snippet['kicker'])->toBe('Valorisation');
    expect($snippet['title'])->toBe('Devise par défaut');
    expect($snippet['note']['title'])->toBe('Pas une conversion');
});

it('serves the currency help in every supported language', function (string $locale, string $expectedTitle) {
    $snippet = app(HelpSnippets::class)->find('settings.general.currency', $locale);

    expect($snippet)->not->toBeNull();
    expect($snippet['title'])->toBe($expectedTitle);
})->with([
    ['en', 'Default currency'],
    ['fr_FR', 'Devise par défaut'],
    ['es_ES', 'Moneda predeterminada'],
    ['de_DE', 'Standardwährung'],
    ['pt_BR', 'Moeda padrão'],
    ['zh_CN', '默认货币'],
    ['ja_JP', 'デフォルト通貨'],
]);

it('serves the account name help in every supported language', function (string $locale, string $expectedTitle) {
    $snippet = app(HelpSnippets::class)->find('settings.general.account_name', $locale);

    expect($snippet)->not->toBeNull();
    expect($snippet['title'])->toBe($expectedTitle);
})->with([
    ['en', 'Account name'],
    ['fr_FR', 'Nom du compte'],
    ['es_ES', 'Nombre de la cuenta'],
    ['de_DE', 'Kontoname'],
    ['pt_BR', 'Nome da conta'],
    ['zh_CN', '账户名称'],
    ['ja_JP', 'アカウント名'],
]);

it('falls back to the default locale when a snippet is not translated', function () {
    $path = storage_path('framework/testing/help/en');
    Illuminate\Support\Facades\File::ensureDirectoryExists($path);
    Illuminate\Support\Facades\File::put($path.'/only.en.md', "---\nid: only.en\ntitle: English only\n---\nBody.");

    config(['docs.help_path' => storage_path('framework/testing/help')]);

    $snippet = (new HelpSnippets(app(DocumentationPortal::class)))->find('only.en', 'fr_FR');

    Illuminate\Support\Facades\File::deleteDirectory(storage_path('framework/testing/help'));

    expect($snippet)->not->toBeNull();
    expect($snippet['title'])->toBe('English only');
});

it('knows which ids it can resolve', function () {
    $help = app(HelpSnippets::class);

    expect($help->knows('settings.general.currency', 'en'))->toBeTrue();
    expect($help->knows('nope.nope', 'en'))->toBeFalse();
});

it('leaves the url null when a snippet names no documentation page', function () {
    $path = storage_path('framework/testing/help/en');
    File::ensureDirectoryExists($path);
    File::put($path.'/standalone.hint.md', "---\nid: standalone.hint\ntitle: A standalone hint\n---\nJust a blurb, no documentation behind it.");

    config(['docs.help_path' => storage_path('framework/testing/help')]);

    $snippet = (new HelpSnippets(app(DocumentationPortal::class)))->find('standalone.hint', 'en');

    File::deleteDirectory(storage_path('framework/testing/help'));

    expect($snippet)->not->toBeNull();
    expect($snippet['url'])->toBeNull();
    expect($snippet['title'])->toBe('A standalone hint');
});
