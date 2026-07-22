<?php

declare(strict_types=1);

use App\Actions\WithdrawTestimonial;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes the member own testimonial', function () {
    Queue::fake();
    $rachel = $this->createUser();
    $testimonial = Testimonial::factory()->published()->create(['user_id' => $rachel->id]);

    new WithdrawTestimonial(user: $rachel, testimonial: $testimonial)->execute();

    $this->assertModelMissing($testimonial);
});

it('logs the withdrawal', function () {
    Queue::fake();
    $rachel = $this->createUser();
    $testimonial = Testimonial::factory()->create(['user_id' => $rachel->id]);

    new WithdrawTestimonial(user: $rachel, testimonial: $testimonial)->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TestimonialWithdrawn);
});

it('forbids withdrawing a testimonial that belongs to someone else', function () {
    Queue::fake();
    $rachel = $this->createUser();
    $monica = $this->createUser();
    $testimonial = Testimonial::factory()->create(['user_id' => $monica->id]);

    expect(fn () => new WithdrawTestimonial(user: $rachel, testimonial: $testimonial)->execute())
        ->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($testimonial);
});
