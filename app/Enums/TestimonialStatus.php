<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a testimonial sits on its way to the marketing site.
 *
 * A member writes one and submits it, which puts it in review. An instance
 * administrator then publishes it, so it appears on the public homepage, or
 * rejects it. A rejected testimonial can be revised and resubmitted, which puts
 * it back in review. Draft exists for a saved but not yet submitted testimonial.
 */
enum TestimonialStatus: string
{
    case Draft = 'draft';
    case InReview = 'in_review';
    case Published = 'published';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::InReview => __('In review'),
            self::Published => __('Published'),
            self::Rejected => __('Rejected'),
        };
    }

    /**
     * The badge colour the status shows as.
     *
     * A published testimonial reads as positive, a rejected one as an error, and
     * an in-review or draft one as neutral or pending.
     */
    public function color(): ?string
    {
        return match ($this) {
            self::Published => 'emerald',
            self::InReview => 'orange',
            self::Rejected => 'error',
            self::Draft => null,
        };
    }

    /**
     * Whether the member may still edit and resubmit the testimonial. A rejected
     * or draft one is theirs to revise; one in review or already published is not.
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::Rejected], true);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
