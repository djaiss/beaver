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
     * A single documentation page.
     */
    public function show(string $locale, string $section, string $slug): View
    {
        $locale = $this->resolveLocale($locale);

        $resolved = $this->portal->find($locale, $section, $slug);

        if ($resolved === null) {
            throw new NotFoundHttpException;
        }

        return $this->renderPage($locale, $resolved);
    }
}
