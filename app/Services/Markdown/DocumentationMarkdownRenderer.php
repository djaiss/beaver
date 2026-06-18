<?php

declare(strict_types=1);

namespace App\Services\Markdown;

use App\Markdown\Extension\ApiDocumentationExtension;
use App\Markdown\Extension\ConfigurationValueExtension;
use Illuminate\Support\Str;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;

class DocumentationMarkdownRenderer
{
    public function render(string $markdown): string
    {
        return Str::markdown(
            $markdown,
            [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'heading_permalink' => [
                    'id_prefix' => '',
                    'fragment_prefix' => '',
                    'symbol' => '#',
                    'insert' => 'after',
                    'html_class' => 'heading-anchor',
                ],
            ],
            [
                new HeadingPermalinkExtension,
                new ConfigurationValueExtension([
                    'app.name' => (string) config('app.name'),
                    'app.url' => (string) config('app.url'),
                ]),
                new ApiDocumentationExtension,
            ],
        );
    }
}
