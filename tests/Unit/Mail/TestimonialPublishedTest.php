<?php

declare(strict_types=1);

use App\Mail\TestimonialPublished;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has a celebratory envelope subject', function () {
    $testimonial = Testimonial::factory()->published()->create();

    $mailable = new TestimonialPublished(testimonial: $testimonial);

    expect($mailable->envelope()->subject)->toContain('live');
});

it('greets the author by first name and links to the testimonials page', function () {
    $author = $this->createUser(['first_name' => 'Gunther']);
    $testimonial = Testimonial::factory()->published()->create(['user_id' => $author->id]);

    $mailable = new TestimonialPublished(testimonial: $testimonial);
    $rendered = $mailable->render();

    expect($rendered)->toContain('Gunther')
        ->and($rendered)->toContain(route('marketing.testimonials.index', ['locale' => 'en']));
});
