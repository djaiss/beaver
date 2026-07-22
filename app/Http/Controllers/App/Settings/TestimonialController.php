<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\SubmitTestimonial;
use App\Actions\WithdrawTestimonial;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureMarketingIsServed();

        return view('app.settings.testimonials.index', [
            'testimonial' => $request->user()->testimonial,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $this->ensureMarketingIsServed();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            // A link is optional, but when present it must be a real http(s) URL.
            // Blocking every other scheme here is what keeps a javascript: or
            // data: payload from ever reaching the public marketing page.
            'link' => ['nullable', 'string', 'max:255', 'url', 'starts_with:http://,https://'],
            'body' => ['required', 'string', 'min:20', 'max:500'],
        ]);

        new SubmitTestimonial(
            user: $request->user(),
            name: $validated['name'],
            link: $validated['link'] ?? null,
            body: $validated['body'],
        )->execute();

        return to_route('settings.testimonials.index')
            ->with('status', __('Thanks so much for your testimonial!'))
            ->with('status_description', __('It is currently in review and will be live soon.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->ensureMarketingIsServed();

        $testimonial = $request->user()->testimonial;

        if ($testimonial === null) {
            throw new ModelNotFoundException('Testimonial not found');
        }

        new WithdrawTestimonial(
            user: $request->user(),
            testimonial: $testimonial,
        )->execute();

        return to_route('settings.testimonials.index')
            ->with('status', __('Your testimonial was removed.'))
            ->with('status_description', __('It is no longer shown on the marketing site. You can submit a new one whenever you like.'));
    }

    /**
     * Testimonials only make sense when the marketing site is served, so the
     * whole section answers 404 otherwise, the same way the sidebar hides it.
     */
    private function ensureMarketingIsServed(): void
    {
        abort_unless(config('marketing.show'), 404);
    }
}
