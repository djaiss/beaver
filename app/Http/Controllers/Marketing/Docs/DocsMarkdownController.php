<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Services\DocNavigationBuilder;
use Illuminate\Http\Response;

class DocsMarkdownController extends Controller
{
    public function show(string $version, string $path): Response
    {
        $filePath = (new DocNavigationBuilder)->resolve($version, $path);

        if ($filePath === null || ! str_ends_with($filePath, '.md')) {
            abort(404);
        }

        $markdown = file_get_contents($filePath);

        if ($markdown === false) {
            abort(404);
        }

        $markdown = preg_replace(
            '/^:::(?:\/)?(?:markdown-actions|copy-for-llm|view-as-markdown)(?:\s+.*?)?\s*$(?:\R)?/m',
            '',
            $markdown,
        ) ?? $markdown;

        return response($markdown, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline',
        ]);
    }
}
