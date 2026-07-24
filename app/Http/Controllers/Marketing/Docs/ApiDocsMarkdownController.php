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
        return response($documentation->markdown(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Get one section of the API reference as markdown. The {locale} URL prefix
     * is absorbed by the parameter of the same name; the API reference itself is
     * not translated.
     */
    public function show(ApiDocumentation $documentation, string $locale, string $section): Response
    {
        $sectionData = $documentation->section($section);

        if ($sectionData === null) {
            abort(404);
        }

        return response($sectionData['markdown'], 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline',
        ]);
    }
}
