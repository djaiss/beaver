<?php

declare(strict_types=1);

use App\Actions\PublishTestimonial;
use App\Enums\EmailType;
use App\Enums\TestimonialStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Mail\TestimonialPublished;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('publishes a testimonial and stamps the publication time', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    new PublishTestimonial(user: $monica, testimonial: $testimonial)->execute();

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Published)
        ->and($testimonial->fresh()->published_at)->not->toBeNull();
});

it('emails the author when publishing', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $author = $this->createUser();
    $testimonial = Testimonial::factory()->create([
        'user_id' => $author->id,
        'status' => TestimonialStatus::InReview,
    ]);

    new PublishTestimonial(user: $monica, testimonial: $testimonial)->execute();

    Queue::assertPushed(SendEmail::class, function (SendEmail $job) use ($author): bool {
        return $job->mailable instanceof TestimonialPublished
            && $job->user->is($author)
            && $job->emailType === EmailType::TestimonialPublished;
    });
});

it('logs the publication', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    new PublishTestimonial(user: $monica, testimonial: $testimonial)->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TestimonialPublished);
});

it('lets an administrator publish a previously rejected testimonial', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $testimonial = Testimonial::factory()->rejected()->create();

    new PublishTestimonial(user: $monica, testimonial: $testimonial)->execute();

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::Published);
});

it('forbids a user who does not administer the instance', function () {
    Queue::fake();
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $testimonial = Testimonial::factory()->create(['status' => TestimonialStatus::InReview]);

    expect(fn () => new PublishTestimonial(user: $rachel, testimonial: $testimonial)->execute())
        ->toThrow(ModelNotFoundException::class);

    expect($testimonial->fresh()->status)->toBe(TestimonialStatus::InReview);
});
