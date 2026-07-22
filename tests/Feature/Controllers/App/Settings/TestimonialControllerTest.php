<?php

declare(strict_types=1);

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('marketing.show', true);
});

it('shows the submission form when the member has no testimonial', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->get(route('settings.testimonials.index'))
        ->assertOk()
        ->assertSee('Say a few words')
        ->assertSee('English')
        // The inline help popovers render their snippet bodies server-side.
        ->assertSee('your name becomes a clickable link next to your words');
});

it('shows the in-review state after submitting', function () {
    $user = $this->createUser();
    Testimonial::factory()->create([
        'user_id' => $user->id,
        'status' => TestimonialStatus::InReview,
        'body' => 'The custom fields feature is what finally won me over here.',
    ]);

    $this->actingAs($user)
        ->get(route('settings.testimonials.index'))
        ->assertOk()
        ->assertSee('currently in review')
        ->assertSee('The custom fields feature is what finally won me over here.');
});

it('makes clear the testimonial can be revoked at any time', function () {
    $user = $this->createUser();
    Testimonial::factory()->published()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('settings.testimonials.index'))
        ->assertOk()
        ->assertSee('revoke your testimonial at any time')
        ->assertSee('Remove my testimonial');
});

it('submits a testimonial and puts it in review', function () {
    Queue::fake();
    $user = $this->createUser();

    $this->actingAs($user)
        ->post(route('settings.testimonials.create'), [
            'name' => 'Rachel Green',
            'link' => 'https://example.com/rachel',
            'body' => 'A place for everything, which is more than I can say for my closet.',
        ])
        ->assertRedirect(route('settings.testimonials.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('testimonials', [
        'user_id' => $user->id,
        'status' => TestimonialStatus::InReview->value,
    ]);
});

it('rejects a link that is not an http scheme', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->post(route('settings.testimonials.create'), [
            'name' => 'Rachel Green',
            'link' => 'javascript:alert(1)',
            'body' => 'A body that is comfortably long enough to be valid here.',
        ])
        ->assertSessionHasErrors('link');

    $this->assertDatabaseCount('testimonials', 0);
});

it('rejects a body that is too short', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->post(route('settings.testimonials.create'), [
            'name' => 'Rachel Green',
            'body' => 'Too short',
        ])
        ->assertSessionHasErrors('body');
});

it('withdraws the member testimonial', function () {
    Queue::fake();
    $user = $this->createUser();
    $testimonial = Testimonial::factory()->published()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('settings.testimonials.destroy'))
        ->assertRedirect(route('settings.testimonials.index'))
        ->assertSessionHas('status');

    $this->assertModelMissing($testimonial);
});

it('answers 404 when the marketing site is off', function () {
    config()->set('marketing.show', false);
    $user = $this->createUser();

    $this->actingAs($user)
        ->get(route('settings.testimonials.index'))
        ->assertNotFound();
});
