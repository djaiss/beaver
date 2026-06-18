<?php

declare(strict_types=1);

namespace App\Markdown\Renderer;

use App\Markdown\Node\DocsDirective;
use Illuminate\Support\HtmlString;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class DocsDirectiveRenderer implements NodeRendererInterface
{
    /**
     * @param  DocsDirective  $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        DocsDirective::assertInstanceOf($node);

        return view("components.marketing.docs.markdown-directives.{$node->name}", [
            'content' => new HtmlString($childRenderer->renderNodes($node->children())),
            'options' => $node->options,
        ])->render();
    }
}
