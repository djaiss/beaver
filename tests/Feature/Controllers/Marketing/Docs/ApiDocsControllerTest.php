<?php

declare(strict_types=1);

it('shows the api docs portal', function () {
    $response = $this->get('/docs');

    $response
        ->assertOk()
        ->assertSee('Introduction')
        ->assertSee('Authentication')
        ->assertSee('List collections')
        ->assertSee('Create a location')
        ->assertSee('Create a condition')
        ->assertSee('Create a tag')
        ->assertSee('Create a category')
        ->assertSee('Create a set')
        ->assertSee('Create an item')
        ->assertSee('Create a copy')
        ->assertSee('Upload an item photo')
        ->assertSee('List item activity')
        ->assertSee('Get API key');
});

it('shows the whole reference as markdown', function () {
    $response = $this->get('/docs.md');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('# '.config('app.name').' API reference', false)
        ->assertSee('## List collections', false);
});

it('shows a single section as markdown', function () {
    $response = $this->get('/docs/collections-list.md');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('## List collections', false)
        ->assertSee('GET', false);
});

it('returns not found for an unknown section', function () {
    $response = $this->get('/docs/unknown-section.md');

    $response->assertNotFound();
});
