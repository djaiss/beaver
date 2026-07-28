<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Who is behind KolleK, why it exists and what it deliberately refuses to
     * build. The copy is static, so it lives in the view rather than in a view
     * model. The {locale} URL prefix is consumed and validated by the
     * marketing.locale middleware, so no locale argument is needed here.
     */
    public function index(): View
    {
        return view('marketing.about');
    }
}
