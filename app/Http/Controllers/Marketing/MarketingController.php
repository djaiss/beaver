<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function index(): View
    {
        // The homepage shows up to six of the most recently published
        // testimonials, and hides the whole section when there are none.
        return view('marketing.index', [
            'testimonials' => Testimonial::query()->published()->take(6)->get(),
            'testimonialCount' => Testimonial::query()->published()->count(),
        ]);
    }
}
