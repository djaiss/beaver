<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TestimonialStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * Reject a testimonial so it does not appear on the marketing site. Only an
 * instance administrator may do this, and the action checks the flag itself.
 *
 * This also serves as the "unpublish" path: taking a published testimonial back
 * down moves it to rejected and clears its publication.
 */
class RejectTestimonial
{
    public function __construct(
        private readonly User $user,
        private readonly Testimonial $testimonial,
    ) {}

    public function execute(): Testimonial
    {
        $this->validate();
        $this->reject();
        $this->flushMarketingCache();
        $this->log();

        return $this->testimonial;
    }

    private function validate(): void
    {
        if (! $this->user->isInstanceAdministrator()) {
            throw new ModelNotFoundException('Testimonial not found');
        }
    }

    private function reject(): void
    {
        $this->testimonial->update([
            'status' => TestimonialStatus::Rejected,
            'published_at' => null,
        ]);
    }

    /**
     * Unpublishing must also drop the testimonial from the response-cached
     * marketing pages, so clear the cache the same way publishing does.
     */
    private function flushMarketingCache(): void
    {
        ResponseCache::clear();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TestimonialRejected,
            parameters: ['name' => $this->testimonial->name],
        )->onQueue('low');
    }
}
