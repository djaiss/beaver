<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Marketing\Docs\Concerns\RendersDocumentationPage;
use App\Services\DocumentationParser;
use App\Services\DocumentationPortal;
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
     * The locale home page, rendered from the top level Markdown file.
     */
    public function show(string $locale): View
    {
        $locale = $this->resolveLocale($locale);

        $resolved = $this->portal->home($locale);

        if ($resolved === null) {
            throw new NotFoundHttpException;
        }

        return $this->renderPage($locale, $resolved);
    }
}
