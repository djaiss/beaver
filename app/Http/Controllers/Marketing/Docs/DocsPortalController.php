<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Marketing\Docs\Concerns\RendersDocumentationPage;
use App\Services\DocumentationParser;
use App\Services\DocumentationPortal;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocsPortalController extends Controller
{
    use RendersDocumentationPage;

    public function __construct(
        private DocumentationPortal $portal,
        private DocumentationParser $parser,
    ) {}

    /**
     * Send the bare /docs URL to the default locale home page.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('marketing.docs.portal.home.show', [
            'locale' => $this->portal->urlLocaleFor($this->portal->defaultLocale()),
        ]);
    }

    /**
     * A single documentation page. The {locale} URL prefix is consumed and
     * validated by the marketing.locale middleware, which sets the app locale;
     * the prefix argument is only here to absorb the route parameter, so the
     * internal locale is read from the app locale instead.
     */
    public function show(string $locale, string $section, string $slug): View
    {
        $locale = app()->getLocale();

        $resolved = $this->portal->find($locale, $section, $slug);

        // Localized URLs are strict: a page is reached only at its own locale's
        // section and slug. A match that only resolves through the English
        // fallback means an English path was used under a translated locale, so
        // it is treated as not found rather than served as duplicate content.
        if ($resolved === null || $resolved['fallback']) {
            throw new NotFoundHttpException;
        }

        return $this->renderPage($locale, $resolved);
    }
}
