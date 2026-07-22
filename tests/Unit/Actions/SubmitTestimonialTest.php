<?php

declare(strict_types=1);

use App\Actions\SubmitTestimonial;
use App\Enums\TestimonialStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a testimonial in review for the user', function () {
    Queue::fake();
    $rachel = $this->createUser();

    $testimonial = new SubmitTestimonial(
        user: $rachel,
        name: 'Rachel Green',
        link: 'https://example.com/rachel',
        body: 'It has a place for everything, which is more than I can say for my closet.',
    )->execute();

    expect($testimonial)->toBeInstanceOf(Testimonial::class)
        ->and($testimonial->status)->toBe(TestimonialStatus::InReview)
        ->and($testimonial->submitted_at)->not->toBeNull();

    $this->assertDatabaseHas('testimonials', [
        'user_id' => $rachel->id,
        'status' => TestimonialStatus::InReview->value,
    ]);
});

it('normalizes a blank link to null', function () {
    Queue::fake();
    $rachel = $this->createUser();

    $testimonial = new SubmitTestimonial(
        user: $rachel,
        name: 'Rachel Green',
        link: '   ',
        body: 'A perfectly good testimonial with no link at all here.',
    )->execute();

    expect($testimonial->link)->toBeNull();
});

it('overwrites the single testimonial when the user resubmits, and clears the publication', function () {
    Queue::fake();
    $rachel = $this->createUser();
    Testimonial::factory()->rejected()->create([
        'user_id' => $rachel->id,
        'body' => 'The old rejected words that need revising now.',
    ]);

    new SubmitTestimonial(
        user: $rachel,
        name: 'Rachel Green',
        link: null,
        body: 'Revised and resubmitted, hopefully this one makes the cut.',
    )->execute();

    expect(Testimonial::query()->where('user_id', $rachel->id)->count())->toBe(1);

    $testimonial = $rachel->testimonial()->first();
    expect($testimonial->status)->toBe(TestimonialStatus::InReview)
        ->and($testimonial->published_at)->toBeNull()
        ->and($testimonial->body)->toBe('Revised and resubmitted, hopefully this one makes the cut.');
});

it('logs the submission', function () {
    Queue::fake();
    $rachel = $this->createUser();

    new SubmitTestimonial(
        user: $rachel,
        name: 'Rachel Green',
        link: null,
        body: 'A testimonial long enough to pass validation comfortably.',
    )->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TestimonialSubmitted);
});
