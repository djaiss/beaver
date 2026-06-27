<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Services\DocNavigationBuilder;
use Illuminate\Contracts\View\View;

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
                'docs.markdown_url' => route('marketing.docs.markdown.show', [
                    'version' => $version,
                    'path' => $path,
                ]),
            ]);
        }

        return view()->file($filePath, ['breadcrumbs' => $breadcrumbs]);
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
