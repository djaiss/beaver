<?php

declare(strict_types = 1);

namespace Tests\Unit\Services\Markdown;

use App\Services\Markdown\DocumentationMarkdownRenderer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentationMarkdownRendererTest extends TestCase
{
    #[Test]
    public function it_renders_api_documentation_directives_and_configuration_values(): void
    {
        config()->set('app.name', 'Test OS');
        config()->set('app.url', 'https://test-os.test');

        $html = resolve(DocumentationMarkdownRenderer::class)->render(<<<'MARKDOWN'
            # Introduction

            :::section columns
            :::column
            {{app.name}}
            :::/column

            :::column
            :::code title="Health" verb="GET"
            ```json
            {"url": "{{app.url}}/api/health"}
            ```
            :::/code
            :::/column
            :::/section
            MARKDOWN);

        $this->assertStringContainsString('Test OS', $html);
        $this->assertStringContainsString('https://test-os.test/api/health', $html);
        $this->assertStringContainsString('sm:grid-cols-2', $html);
        $this->assertStringContainsString('Health', $html);
        $this->assertStringContainsString('GET', $html);
        $this->assertStringNotContainsString(':::section', $html);
    }

    #[Test]
    public function it_preserves_indentation_inside_fenced_code_blocks(): void
    {
        $html = resolve(DocumentationMarkdownRenderer::class)->render(<<<'MARKDOWN'
            :::code title="Pagination"

            ```json
            {
              "meta": {
                "current_page": 1
              }
            }
            ```
            :::/code
            MARKDOWN);

        $this->assertStringContainsString(
            "{\n  &quot;meta&quot;: {\n    &quot;current_page&quot;: 1\n  }\n}",
            $html,
        );
    }

    #[Test]
    public function it_renders_markdown_action_directives(): void
    {
        $html = resolve(DocumentationMarkdownRenderer::class)->render(
            <<<'MARKDOWN'
                :::markdown-actions url="{{docs.markdown_url}}"
                :::copy-for-llm
                :::/copy-for-llm

                :::view-as-markdown url="{{docs.markdown_url}}"
                :::/view-as-markdown
                :::/markdown-actions
                MARKDOWN,
            ['docs.markdown_url' => 'https://lifeos.test/docs/1.x/api/profile.md'],
        );

        $this->assertStringContainsString('Copy for LLM', $html);
        $this->assertStringContainsString('View as Markdown', $html);
        $this->assertStringContainsString('https://lifeos.test/docs/1.x/api/profile.md', $html);
        $this->assertStringNotContainsString(':::markdown-actions', $html);
    }
}
