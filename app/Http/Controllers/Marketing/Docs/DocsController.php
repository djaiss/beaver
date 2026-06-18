<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Services\Markdown\DocumentationMarkdownRenderer;
use Illuminate\View\View;

class DocsController extends Controller
{
    public function __construct(
        private readonly DocumentationMarkdownRenderer $markdownRenderer,
    ) {}

    /**
     * @param  array<int, array{label: string, route?: string}>  $breadcrumbs
     * @param  array<string, string>  $configurationValues
     */
    protected function renderDoc(string $absoluteFilePath, array $breadcrumbs, array $configurationValues = []): View
    {
        $markdown = file_get_contents($absoluteFilePath);

        if ($markdown === false) {
            abort(404);
        }

        return view('marketing.docs.markdown', [
            'content' => $this->markdownRenderer->render($markdown, $configurationValues),
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
