<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;

class DocsController extends Controller
{
    protected function renderDoc(string $absoluteFilePath, array $breadcrumbs): View
    {
        $content = Str::of(
            file_get_contents($absoluteFilePath)
        )->markdown(
            [
                'html_input' => 'strip',
                'heading_permalink' => [
                    'id_prefix' => '',
                    'fragment_prefix' => '',
                    'symbol' => '#',
                    'insert' => 'after',
                    'html_class' => 'heading-anchor',
                ],
            ],
            [new HeadingPermalinkExtension],
        );

        return view('marketing.docs.markdown', ['content' => $content, 'breadcrumbs' => $breadcrumbs]);
    }
}
