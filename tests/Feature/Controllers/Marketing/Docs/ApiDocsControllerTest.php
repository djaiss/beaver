<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('shows the api docs portal', function () {
    $response = $this->get('/en/docs/api');

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
        ->assertSee('Get API key')
        ->assertSee('Get the statistics of a collection')
        ->assertSee('List the trash')
        ->assertSee('Get the account')
        ->assertSee('List members')
        ->assertSee('List pending invitations');
});

it('shows the whole reference as markdown', function () {
    $response = $this->get('/en/docs/api.md');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('# '.config('app.name').' API reference', false)
        ->assertSee('## List collections', false);
});

it('shows a single section as markdown', function () {
    $response = $this->get('/en/docs/api/collections-list.md');

    $response
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('## List collections', false)
        ->assertSee('GET', false);
});

it('returns not found for an unknown section', function () {
    $response = $this->get('/en/docs/api/unknown-section.md');

    $response->assertNotFound();
});
