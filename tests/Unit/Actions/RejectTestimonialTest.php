<?php

declare(strict_types=1);

use App\Actions\RejectTestimonial;
use App\Enums\TestimonialStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('rejects a testimonial in review', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    new RejectTestimonial(user: $monica, testimonial: $testimonial)->execute();

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Rejected);
});

it('unpublishes a published testimonial, clearing its publication', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->published()->create();

    new RejectTestimonial(user: $monica, testimonial: $testimonial)->execute();

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Rejected)
        ->and($testimonial->fresh()->published_at)->toBeNull();
});

it('logs the rejection', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    new RejectTestimonial(user: $monica, testimonial: $testimonial)->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TestimonialRejected);
});

it('forbids a user who does not administer the instance', function () {
    Queue::fake();
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $testimonial = Testimonial::factory()->published()->create();

    expect(fn () => new RejectTestimonial(user: $rachel, testimonial: $testimonial)->execute())
        ->toThrow(ModelNotFoundException::class);

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Published);
});
