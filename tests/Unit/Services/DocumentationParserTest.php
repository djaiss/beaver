<?php

declare(strict_types=1);

use App\Services\DocumentationParser;
use App\Services\DocumentationPortal;

beforeEach(function (): void {
    config()->set('marketing.show', true);
    $this->parser = app(DocumentationParser::class);
});

it('splits frontmatter from the body', function () {
    $raw = "---\nid: demo.page\ntitle: Demo\nslug: demo\nsection: getting-started\n---\n\n# Demo\n\nHello.";

    $result = $this->parser->split($raw);

    expect($result['meta']['id'])->toBe('demo.page')
        ->and($result['body'])->toStartWith('# Demo');
});

it('drops the leading level one heading because the title renders separately', function () {
    $result = $this->parser->render("# Demo\n\nA paragraph.", 'en');

    expect($result['html'])->not->toContain('<h1>')
        ->and($result['html'])->toContain('A paragraph.');
});

it('resolves a doc directive to a portal url using the target page title', function () {
    $portal = app(DocumentationPortal::class);
    $page = collect($portal->pagesFor('en'))->firstWhere('id', 'kollek.whatIs');
    $url = $portal->urlFor('en', $page);

    $result = $this->parser->render('See @doc(kollek.whatIs) for details.', 'en');

    expect($result['html'])->toContain($url)
        ->and($result['html'])->toContain('What is KolleK');
});

it('uses a custom label when the doc directive provides one', function () {
    $result = $this->parser->render('Read @doc(kollek.whatIs, "the intro") now.', 'en');

    expect($result['html'])->toContain('the intro');
});

it('renders a note admonition', function () {
    $result = $this->parser->render(":::note\nRemember this.\n:::", 'en');

    expect($result['html'])->toContain('doc-admonition')
        ->and($result['html'])->toContain('Remember this.')
        ->and($result['html'])->toContain('bg-blue-50');
});

it('renders a warning admonition with a custom title', function () {
    $result = $this->parser->render(':::warning title="Careful"'."\nDanger ahead.\n:::", 'en');

    expect($result['html'])->toContain('Careful')
        ->and($result['html'])->toContain('bg-amber-50');
});

it('renders a step walkthrough with numbered steps', function () {
    $body = "::::steps\n:::step title=\"First\"\nDo the first thing.\n:::\n\n:::step title=\"Second\"\nThen the second.\n:::\n::::";

    $result = $this->parser->render($body, 'en');

    expect($result['html'])->toContain('First')
        ->and($result['html'])->toContain('Second')
        ->and($result['html'])->toContain('Do the first thing.')
        // The step numbers render inside the circled markers.
        ->and($result['html'])->toContain('>1</div>')
        ->and($result['html'])->toContain('>2</div>');
});

it('builds a table of contents from h2 and h3 headings', function () {
    $body = "## First section\n\nText.\n\n### A detail\n\nMore.\n\n## Second section\n\nText.";

    $result = $this->parser->render($body, 'en');

    expect($result['toc'])->toHaveCount(3)
        ->and($result['toc'][0])->toMatchArray(['id' => 'first-section', 'level' => 2])
        ->and($result['toc'][1])->toMatchArray(['id' => 'a-detail', 'level' => 3])
        ->and($result['html'])->toContain('id="first-section"');
});

it('reports the doc references found in a body', function () {
    $references = $this->parser->docReferences('First @doc(a.one) then @doc(b.two, "label") and @doc(a.one).');

    expect($references)->toBe(['a.one', 'b.two']);
});

it('drives internal content links through turbo but leaves external ones alone', function () {
    config()->set('app.url', 'https://getkollek.com');

    $body = '[Absolute internal](https://getkollek.com/en/pricing) and '
        .'[Root relative](/en/docs) and '
        .'[External](https://github.com/kollek) and '
        .'[Anchor](#section) and '
        .'[Protocol relative](//cdn.example.com/x).';

    $result = $this->parser->render($body, 'en');

    expect($result['html'])
        ->toContain('<a data-turbo="true" href="https://getkollek.com/en/pricing">')
        ->toContain('<a data-turbo="true" href="/en/docs">')
        ->toContain('<a href="https://github.com/kollek">')
        ->toContain('<a href="#section">')
        ->toContain('<a href="//cdn.example.com/x">');
});

it('drives a resolved doc directive link through turbo', function () {
    $result = $this->parser->render('See @doc(kollek.whatIs) for details.', 'en');

    expect($result['html'])->toContain('data-turbo="true"');
});
