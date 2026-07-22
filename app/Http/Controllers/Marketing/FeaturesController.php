<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\MarketingFeatures;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FeaturesController extends Controller
{
    public function __construct(
        private MarketingFeatures $features,
    ) {}

    /**
     * The features hub: a visual index of every feature area. The {locale} URL
     * prefix is consumed and validated by the marketing.locale middleware, so no
     * locale argument is needed here.
     */
    public function index(): View
    {
        return view('marketing.features.index', [
            'columns' => $this->features->columns(),
        ]);
    }

    /**
     * A single feature page. The {locale} prefix is the first route parameter, so
     * it is absorbed here even though the locale itself comes from the app locale
     * the middleware already set.
     */
    public function show(string $locale, string $slug): View
    {
        $feature = $this->features->find($slug);

        if ($feature === null) {
            throw new NotFoundHttpException;
        }

        return view('marketing.features.show', [
            'feature' => $feature,
        ]);
    }
}
