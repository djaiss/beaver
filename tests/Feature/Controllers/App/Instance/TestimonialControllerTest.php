<?php

declare(strict_types=1);

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the testimonials waiting for review by default', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    Testimonial::factory()->create([
        'status' => TestimonialStatus::InReview,
        'body' => 'A pending testimonial that the admin should see first here.',
    ]);
    Testimonial::factory()->published()->create([
        'body' => 'An already published one that lives in another bucket.',
    ]);

    $this->actingAs($monica)
        ->get(route('instanceAdmin.marketing.testimonials.index'))
        ->assertOk()
        ->assertSee('A pending testimonial that the admin should see first here.')
        ->assertDontSee('An already published one that lives in another bucket.');
});

it('filters by the status bucket in the path', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    Testimonial::factory()->published()->create([
        'body' => 'The published testimonial shown under the published tab.',
    ]);

    $this->actingAs($monica)
        ->get(route('instanceAdmin.marketing.testimonials.index', ['status' => 'published']))
        ->assertOk()
        ->assertSee('The published testimonial shown under the published tab.');
});

it('publishes a testimonial from the panel', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    $this->actingAs($monica)
        ->put(route('instanceAdmin.marketing.testimonials.update', $testimonial->id), ['intent' => 'publish'])
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Published);
});

it('rejects a testimonial from the panel', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    $this->actingAs($monica)
        ->put(route('instanceAdmin.marketing.testimonials.update', $testimonial->id), ['intent' => 'reject'])
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Rejected);
});

it('requires a valid intent to moderate', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    $this->actingAs($monica)
        ->put(route('instanceAdmin.marketing.testimonials.update', $testimonial->id), ['intent' => 'nonsense'])
        ->assertSessionHasErrors('intent');

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::InReview);
});

it('answers 404 to a user who does not administer the instance', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)
        ->get(route('instanceAdmin.marketing.testimonials.index'))
        ->assertNotFound();
});
