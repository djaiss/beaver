<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\ViewModels\MarketingFaq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(
        private MarketingFaq $faq,
    ) {}

    /**
     * The public FAQ. The {locale} URL prefix is consumed and validated by the
     * marketing.locale middleware, so no locale argument is needed here.
     */
    public function index(): View
    {
        return view('marketing.faq', [
            'quickAnswers' => $this->faq->quickAnswers(),
            'sections' => $this->faq->sections(),
            'totalQuestions' => $this->faq->totalQuestions(),
        ]);
    }
}
