<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('renders the docs index page', function () {
    $response = $this->get('/docs');

    $response->assertOk();
    $response->assertSee('lg:grid-cols-[300px_1fr]', false);
    $response->assertDontSee('lg:grid-cols-[300px_1fr_250px]', false);
});

it('renders a right sidebar when its slot is provided', function () {
    Route::get('/docs-with-right-sidebar', fn (): string => Blade::render(<<<'BLADE'
                <x-marketing-docs-layout>
                    <x-slot:rightSidebar>
                        <p>Right sidebar content</p>
                    </x-slot:rightSidebar>

                    <p>Documentation content</p>
                </x-marketing-docs-layout>
            BLADE))->name('marketing.docs.with-right-sidebar');

    $response = $this->get('/docs-with-right-sidebar');

    $response->assertOk();
    $response->assertSee('lg:grid-cols-[300px_1fr_250px]', false);
    $response->assertSee('Right sidebar content');
});
