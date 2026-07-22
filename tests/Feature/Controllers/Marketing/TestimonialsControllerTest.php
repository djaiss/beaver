<?php

declare(strict_types=1);

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('marketing.show', true);
});

it('lists every published testimonial without pagination', function () {
    Testimonial::factory()->published()->create([
        'name' => 'Marion Delacroix',
        'body' => 'I moved 900 comics off a decade-old spreadsheet in an afternoon.',
    ]);
    Testimonial::factory()->published()->create([
        'name' => 'Tomas Herrera',
        'body' => 'Self-hosted on a Raspberry Pi in one single command.',
    ]);

    $this->get(route('marketing.testimonials.index'))
        ->assertOk()
        ->assertSee('Marion Delacroix')
        ->assertSee('Tomas Herrera');
});

it('never shows testimonials that are not published', function () {
    Testimonial::factory()->create([
        'status' => TestimonialStatus::InReview,
        'name' => 'Still In Review',
        'body' => 'This one is still waiting on a moderator to look at it.',
    ]);
    Testimonial::factory()->rejected()->create([
        'name' => 'Was Rejected',
        'body' => 'This one did not make the cut and must never appear.',
    ]);

    $this->get(route('marketing.testimonials.index'))
        ->assertOk()
        ->assertDontSee('Still In Review')
        ->assertDontSee('Was Rejected');
});

it('renders a submitted link with safe rel attributes', function () {
    Testimonial::factory()->published()->create([
        'name' => 'Linked Collector',
        'link' => 'https://example.com/collector',
    ]);

    $this->get(route('marketing.testimonials.index'))
        ->assertOk()
        ->assertSee('rel="nofollow ugc noopener"', false)
        ->assertSee('https://example.com/collector');
});

it('redirects to login when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.testimonials.index'))->assertRedirect(route('login'));
});
