<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Services\DocumentationParser;
use App\Services\DocumentationPortal;
use App\Traits\RendersDocumentationPage;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocsPortalHomeController extends Controller
{
    use RendersDocumentationPage;

    public function __construct(
        private DocumentationPortal $portal,
        private DocumentationParser $parser,
    ) {}

    /**
     * The locale home page, rendered from the top level Markdown file. The
     * locale is resolved from the URL prefix by the marketing.locale
     * middleware, so it reads it from the app locale.
     */
    public function show(): View
    {
        $locale = app()->getLocale();

        $resolved = $this->portal->home($locale);

        if ($resolved === null || $resolved['fallback']) {
            throw new NotFoundHttpException;
        }

        return $this->renderPage($locale, $resolved);
    }
}
