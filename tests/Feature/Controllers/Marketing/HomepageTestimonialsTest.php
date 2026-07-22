<?php

declare(strict_types=1);

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('marketing.show', true);
});

it('shows the testimonials section on the homepage when there are published testimonials', function () {
    Testimonial::factory()->published()->create([
        'name' => 'Marion Delacroix',
        'body' => 'The nested locations mean I finally know which box things are in.',
    ]);

    $this->get('/en')
        ->assertOk()
        ->assertSee('id="testimonials"', false)
        ->assertSee('Marion Delacroix')
        ->assertSee('Read all');
});

it('hides the testimonials section entirely when nothing is published', function () {
    $this->get('/en')
        ->assertOk()
        ->assertDontSee('id="testimonials"', false)
        ->assertDontSee('Straight from the wall.');
});

it('shows at most six testimonials on the homepage', function () {
    // Every one carries a link, so each rendered card emits exactly one safe
    // anchor and the count of those anchors is the count of cards shown.
    Testimonial::factory()->count(8)->published()->create(['link' => 'https://example.com/collector']);

    $response = $this->get('/en')->assertOk();

    expect(substr_count($response->getContent(), 'nofollow ugc noopener'))->toBe(6);
});
