<?php

declare(strict_types=1);

use App\Enums\TestimonialStatus;

it('exposes a label for every status', function () {
    expect(TestimonialStatus::Draft->label())->toBe('Draft')
        ->and(TestimonialStatus::InReview->label())->toBe('In review')
        ->and(TestimonialStatus::Published->label())->toBe('Published')
        ->and(TestimonialStatus::Rejected->label())->toBe('Rejected');
});

it('maps each status to a badge colour', function () {
    expect(TestimonialStatus::Published->color())->toBe('emerald')
        ->and(TestimonialStatus::InReview->color())->toBe('orange')
        ->and(TestimonialStatus::Rejected->color())->toBe('error')
        ->and(TestimonialStatus::Draft->color())->toBeNull();
});

it('knows which statuses the member may still edit', function () {
    expect(TestimonialStatus::Draft->isEditable())->toBeTrue()
        ->and(TestimonialStatus::Rejected->isEditable())->toBeTrue()
        ->and(TestimonialStatus::InReview->isEditable())->toBeFalse()
        ->and(TestimonialStatus::Published->isEditable())->toBeFalse();
});

it('builds a value to label option map', function () {
    expect(TestimonialStatus::options())->toBe([
        'draft' => 'Draft',
        'in_review' => 'In review',
        'published' => 'Published',
        'rejected' => 'Rejected',
    ]);
});
