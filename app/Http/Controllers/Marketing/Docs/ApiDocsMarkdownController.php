<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Services\ApiDocumentation;
use Illuminate\Http\Response;

class ApiDocsMarkdownController extends Controller
{
    /**
     * Get the whole API reference as one markdown document.
     */
    public function index(ApiDocumentation $documentation): Response
    {
        return $this->plainText($documentation->markdown());
    }

    /**
     * Get one section of the API reference as markdown.
     */
    public function show(ApiDocumentation $documentation, string $section): Response
    {
        $sectionData = $documentation->section($section);

        if ($sectionData === null) {
            abort(404);
        }

        return $this->plainText($sectionData['markdown']);
    }

    private function plainText(string $markdown): Response
    {
        return response($markdown, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline',
        ]);
    }
}
