<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Services\DocNavigationBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class DocsPageController extends DocsController
{
    public function show(string $version, string $path = ''): View
    {
        $builder = new DocNavigationBuilder;
        $filePath = $builder->resolve($version, $path);

        if ($filePath === null) {
            abort(404);
        }

        $breadcrumbs = $this->buildBreadcrumbs($version, $path, $builder);

        if (str_ends_with($filePath, '.md')) {
            return $this->renderDoc($filePath, $breadcrumbs, [
                'docs.markdown_url' => route('marketing.docs.markdown', [
                    'version' => $version,
                    'path' => $path,
                ]),
            ]);
        }

        return view()->file($filePath, ['breadcrumbs' => $breadcrumbs]);
    }

    public function markdown(string $version, string $path): Response
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

    private function buildBreadcrumbs(string $version, string $path, DocNavigationBuilder $builder): array
    {
        $breadcrumbs = [
            ['label' => 'Home', 'route' => route('marketing.index')],
            ['label' => 'Documentation', 'route' => route('marketing.docs.index')],
        ];

        if ($path === '') {
            return $breadcrumbs;
        }

        $segments = explode('/', $path);
        $cumulativePath = '';

        foreach ($segments as $i => $segment) {
            $cumulativePath = $cumulativePath !== '' ? $cumulativePath.'/'.$segment : $segment;
            $label = $builder->toLabel($segment);
            $isLast = $i === count($segments) - 1;

            if ($isLast) {
                $breadcrumbs[] = ['label' => $label];
            } else {
                $breadcrumbs[] = [
                    'label' => $label,
                    'route' => route('marketing.docs.show', ['version' => $version, 'path' => $cumulativePath]),
                ];
            }
        }

        return $breadcrumbs;
    }
}
