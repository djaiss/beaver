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

    protected function renderDoc(string $absoluteFilePath, array $breadcrumbs): View
    {
        $markdown = file_get_contents($absoluteFilePath);

        if ($markdown === false) {
            abort(404);
        }

        return view('marketing.docs.markdown', [
            'content' => $this->markdownRenderer->render($markdown),
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
