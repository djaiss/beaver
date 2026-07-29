<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\Sitemap;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(
        private Sitemap $sitemap,
    ) {}

    /**
     * The whole public site, for a crawler. It sits outside the {locale} prefix
     * because there is one sitemap for every language, not one per language: the
     * alternates inside it are what tie the translations of a page together.
     */
    public function index(): Response
    {
        return response()
            ->view('marketing.sitemap', ['entries' => $this->sitemap->entries()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
