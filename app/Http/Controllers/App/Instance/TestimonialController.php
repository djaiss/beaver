<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\PublishTestimonial;
use App\Actions\RejectTestimonial;
use App\Enums\TestimonialStatus;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Moderating the testimonials members submit for the marketing site. Publishing
 * one puts it on the public homepage and emails its author; rejecting keeps it
 * off. Like the rest of the instance panel this is English only, so its copy is
 * written as plain strings rather than through __().
 */
class TestimonialController extends Controller
{
    /**
     * The testimonials in one status bucket. The bucket lives in the path so each
     * one is its own page; opening the section without a bucket lands on the ones
     * still waiting for review.
     */
    public function index(string $status = 'in_review'): View
    {
        $testimonials = Testimonial::query()
            ->with('user.account')
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->latest('submitted_at')
            ->latest('id')
            ->get();

        return view('app.instance.marketing.testimonials.index', [
            'status' => $status,
            'testimonials' => $testimonials,
            'counts' => $this->counts(),
        ]);
    }

    /**
     * Moderate a testimonial. Publishing puts it live and emails the author;
     * rejecting keeps it off (and unpublishes a live one). Which of the two is
     * carried by the intent field, since both are the same kind of state change.
     */
    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'intent' => ['required', Rule::in(['publish', 'reject'])],
            'return' => ['nullable', Rule::in(['in_review', 'published', 'rejected', 'draft', 'all'])],
        ]);

        if ($validated['intent'] === 'publish') {
            new PublishTestimonial(
                user: $request->user(),
                testimonial: $testimonial,
            )->execute();

            $message = 'Testimonial published and the author was emailed.';
        } else {
            new RejectTestimonial(
                user: $request->user(),
                testimonial: $testimonial,
            )->execute();

            $message = 'Testimonial rejected.';
        }

        return to_route('instanceAdmin.marketing.testimonials.index', ['status' => $validated['return'] ?? 'in_review'])
            ->with('status', $message);
    }

    /**
     * The per-bucket counts shown on the filter tabs.
     *
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'in_review' => Testimonial::query()->where('status', TestimonialStatus::InReview)->count(),
            'published' => Testimonial::query()->where('status', TestimonialStatus::Published)->count(),
            'rejected' => Testimonial::query()->where('status', TestimonialStatus::Rejected)->count(),
            'draft' => Testimonial::query()->where('status', TestimonialStatus::Draft)->count(),
            'all' => Testimonial::query()->count(),
        ];
    }
}
