<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\View\View;

class TestimonialsController extends Controller
{
    /**
     * The public page that lists every published testimonial, without
     * pagination. No {locale} argument is needed: the prefix is pinned as a URL
     * default by the marketing.locale middleware.
     */
    public function index(): View
    {
        return view('marketing.testimonials.index', [
            'testimonials' => Testimonial::query()->published()->get(),
        ]);
    }
}
