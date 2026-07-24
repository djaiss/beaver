<?php

declare(strict_types=1);
use App\Services\ApiDocumentation;
use Illuminate\Support\Facades\Route;

it('documents every api route and documents only real routes', function () {
    $documented = collect(new ApiDocumentation()->documentedRoutes())
        ->map(fn (array $route): string => $route['method'].' api'.$route['path'])
        ->sort()
        ->values()
        ->all();

    $actual = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route): bool => str_starts_with($route->uri(), 'api/'))
        ->flatMap(fn ($route) => collect($route->methods())
            ->reject(fn (string $method): bool => in_array($method, ['HEAD', 'OPTIONS'], true))
            ->map(fn (string $method): string => $method.' '.$route->uri()))
        ->sort()
        ->values()
        ->all();

    expect($documented)->toBe($actual);
});

it('builds the navigation with guides and resource groups', function () {
    $navigation = new ApiDocumentation()->navigation();

    expect($navigation['guides'])->not->toBeEmpty();
    expect($navigation['resources'])->not->toBeEmpty();

    $labels = collect($navigation['resources'])->pluck('name')->all();
    expect($labels)->toContain('Collections');
    expect($labels)->toContain('Locations');

    $catalogs = collect($navigation['resources'])->firstWhere('name', 'Collections');
    expect($catalogs['items'][0])->toHaveKeys(['id', 'label', 'method']);
});

it('builds code samples for every language', function () {
    $section = new ApiDocumentation()->section('collections-create');

    expect($section['samples'])->toHaveKeys(['curl', 'javascript', 'php']);
    expect($section['samples']['curl']['code'])->toContain('curl');
    expect($section['samples']['curl']['code'])->toContain('Authorization: Bearer');
    expect($section['samples']['curl']['code'])->toContain('-X POST');
    expect($section['samples']['javascript']['code'])->toContain('await fetch(');
    expect($section['samples']['php']['code'])->toContain('Http::withToken($apiKey)');
});

it('does not send an authorization header for guest endpoints', function () {
    $section = new ApiDocumentation()->section('auth-login');

    expect($section['samples']['curl']['code'])->not->toContain('Authorization');
    expect($section['samples']['php']['code'])->toContain('Http::acceptJson()');
});

it('builds the markdown of a section', function () {
    $section = new ApiDocumentation()->section('collections-list');

    expect($section['markdown'])->toContain('## List collections');
    expect($section['markdown'])->toContain('`GET '.ApiDocumentation::baseUrl().'/collections`');
    expect($section['markdown'])->toContain('### Query parameters');
    expect($section['markdown'])->toContain('```json');
});

it('describes an empty body for delete endpoints', function () {
    $section = new ApiDocumentation()->section('collections-destroy');

    expect($section['responseJson'])->toBeNull();
    expect($section['markdown'])->toContain('The response has no body.');
});

it('returns null for an unknown section', function () {
    expect(new ApiDocumentation()->section('unknown'))->toBeNull();
});
