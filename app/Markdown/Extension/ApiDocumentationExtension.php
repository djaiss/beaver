<?php

declare(strict_types=1);

namespace App\Markdown\Extension;

use App\Markdown\Node\DocsDirective;
use App\Markdown\Parser\DocsDirectiveStartParser;
use App\Markdown\Renderer\DocsDirectiveRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

class ApiDocumentationExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        foreach ([
            'attribute',
            'code',
            'column',
            'copy-for-llm',
            'description',
            'markdown-actions',
            'parameters',
            'section',
            'toc',
            'view-as-markdown',
        ] as $directive) {
            $environment->addBlockStartParser(new DocsDirectiveStartParser($directive), 100);
        }

        $environment->addRenderer(DocsDirective::class, new DocsDirectiveRenderer);
    }
}
