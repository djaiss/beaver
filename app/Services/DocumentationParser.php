<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/*
 * Turns one documentation page's Markdown body into the HTML the portal renders.
 *
 * On top of plain Markdown it understands three project conventions:
 *   - @doc(id) and @doc(id, "label") inline links to other pages,
 *   - :::note / :::warning admonition blocks,
 *   - ::::steps wrapping :::step title="..." walkthrough blocks.
 *
 * It also collects the h2 and h3 headings into a table of contents and gives
 * each one an id to anchor to.
 */
class DocumentationParser
{
    private MarkdownConverter $markdown;

    public function __construct(
        private DocumentationPortal $portal,
    ) {
        $environment = new Environment([
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);

        $this->markdown = new MarkdownConverter($environment);
    }

    /**
     * Split a raw file into its frontmatter and its body.
     *
     * @return array{meta: array<string, string>|null, body: string}
     */
    public function split(string $raw): array
    {
        $meta = $this->portal->frontmatter($raw);
        $body = preg_replace('/^(?:\xEF\xBB\xBF)?---\s*\n.*?\n---\s*\n/s', '', $raw, 1);

        return ['meta' => $meta, 'body' => ltrim((string) $body)];
    }

    /**
     * Render a body to HTML and its table of contents.
     *
     * @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>}
     */
    public function render(string $body, string $locale): array
    {
        $body = $this->stripLeadingTitle($body);
        $body = $this->resolveDocLinks($body, $locale);

        $html = $this->convert(explode("\n", $body), $locale);

        return $this->extractTableOfContents($html);
    }

    /**
     * Every @doc() identifier referenced in a body, for validation.
     *
     * @return array<int, string>
     */
    public function docReferences(string $body): array
    {
        preg_match_all($this->docPattern(), $body, $matches);

        return array_values(array_unique($matches[1]));
    }

    /**
     * Drop the first heading when it is a level one heading, because the portal
     * renders the frontmatter title as the page heading itself.
     */
    private function stripLeadingTitle(string $body): string
    {
        return preg_replace('/^\s*#\s+.*\n/', '', $body, 1);
    }

    /**
     * Replace @doc(id) and @doc(id, "label") with Markdown links. An unknown
     * identifier renders as its label alone so the page still reads, while the
     * validation test catches the broken reference separately.
     */
    private function resolveDocLinks(string $body, string $locale): string
    {
        return preg_replace_callback($this->docPattern(), function (array $matches) use ($locale): string {
            $id = $matches[1];
            $label = $matches[2] ?? null;
            $url = $this->portal->urlForId($id, $locale);
            $text = $label ?? $this->portal->titleForId($id, $locale) ?? $id;

            if ($url === null) {
                return $text;
            }

            return '['.$text.']('.$url.')';
        }, $body);
    }

    private function docPattern(): string
    {
        return '/@doc\(\s*([a-zA-Z0-9._-]+)\s*(?:,\s*"([^"]*)")?\s*\)/';
    }

    /**
     * Walk the body line by line, rendering plain Markdown through CommonMark
     * and the ::: directives through their own renderers. Nested directives are
     * matched by colon count so a :::step inside ::::steps closes correctly.
     *
     * @param  array<int, string>  $lines
     */
    private function convert(array $lines, string $locale): string
    {
        $html = '';
        $plain = [];
        $inCode = false;
        $count = count($lines);

        for ($i = 0; $i < $count; $i++) {
            $line = $lines[$i];
            $trimmed = rtrim($line);

            if (preg_match('/^(```|~~~)/', ltrim($line))) {
                $inCode = ! $inCode;
                $plain[] = $line;

                continue;
            }

            if (! $inCode && preg_match('/^(:{3,})(\w+)(.*)$/', $trimmed, $matches)) {
                $html .= $this->flush($plain);
                $plain = [];

                $fence = $matches[1];
                $name = $matches[2];
                $attributes = trim($matches[3]);
                $inner = $this->consumeBlock($lines, $i, $fence);

                $html .= $this->renderDirective($name, $attributes, $inner, $locale);

                continue;
            }

            $plain[] = $line;
        }

        return $html.$this->flush($plain);
    }

    /**
     * Consume the lines inside a directive that opened at $i, advancing $i past
     * the closing fence. Directives of the same colon count nest correctly.
     *
     * @param  array<int, string>  $lines
     * @return array<int, string>
     */
    private function consumeBlock(array $lines, int &$i, string $fence): array
    {
        $depth = 1;
        $inner = [];
        $count = count($lines);

        for ($i++; $i < $count; $i++) {
            $trimmed = rtrim($lines[$i]);

            if ($trimmed === $fence) {
                $depth--;

                if ($depth === 0) {
                    return $inner;
                }
            } elseif (preg_match('/^'.$fence.'\w/', $trimmed)) {
                $depth++;
            }

            $inner[] = $lines[$i];
        }

        return $inner;
    }

    /**
     * @param  array<int, string>  $inner
     */
    private function renderDirective(string $name, string $attributes, array $inner, string $locale): string
    {
        return match ($name) {
            'note' => $this->renderAdmonition('note', $attributes, $inner, $locale),
            'warning' => $this->renderAdmonition('warning', $attributes, $inner, $locale),
            'steps' => $this->renderSteps($inner, $locale),
            default => $this->convert($inner, $locale),
        };
    }

    /**
     * @param  array<int, string>  $inner
     */
    private function renderAdmonition(string $kind, string $attributes, array $inner, string $locale): string
    {
        $body = $this->convert($inner, $locale);
        $title = $this->attribute($attributes, 'title')
            ?? ($kind === 'warning' ? __('Warning') : __('Note'));

        $palette = $kind === 'warning'
            ? ['wrap' => 'border-amber-200 bg-amber-50', 'bar' => 'border-l-amber-500', 'title' => 'text-amber-800', 'body' => 'text-amber-900', 'icon' => 'text-amber-700']
            : ['wrap' => 'border-blue-200 bg-blue-50', 'bar' => 'border-l-blue-500', 'title' => 'text-blue-800', 'body' => 'text-blue-900', 'icon' => 'text-blue-600'];

        $icon = $kind === 'warning'
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 '.$palette['icon'].'"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 '.$palette['icon'].'"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>';

        return '<div class="not-prose my-6 flex gap-3.5 rounded-lg border border-l-[3px] '.$palette['wrap'].' '.$palette['bar'].' px-4.5 py-4">'
            .$icon
            .'<div class="min-w-0"><div class="mb-1 text-sm font-semibold '.$palette['title'].'">'.e($title).'</div>'
            .'<div class="doc-admonition text-[15px] leading-relaxed '.$palette['body'].'">'.$body.'</div></div></div>';
    }

    /**
     * @param  array<int, string>  $inner
     */
    private function renderSteps(array $inner, string $locale): string
    {
        $steps = $this->splitSteps($inner);
        $total = count($steps);
        $html = '<div class="not-prose my-7 flex flex-col">';

        foreach ($steps as $index => $step) {
            $number = $index + 1;
            $isLast = $number === $total;
            $connector = $isLast ? '' : '<div class="my-1.5 w-0.5 flex-1 bg-gray-200"></div>';
            $body = $this->convert($step['lines'], $locale);

            $html .= '<div class="flex gap-4.5">'
                .'<div class="flex shrink-0 flex-col items-center">'
                .'<div class="flex h-[30px] w-[30px] items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white">'.$number.'</div>'
                .$connector.'</div>'
                .'<div class="min-w-0 flex-1 pb-7">'
                .($step['title'] !== null ? '<div class="mb-1.5 text-[17px] font-semibold tracking-tight text-gray-900">'.e($step['title']).'</div>' : '')
                .'<div class="doc-step text-[15px] leading-relaxed text-gray-600">'.$body.'</div></div></div>';
        }

        return $html.'</div>';
    }

    /**
     * Break the inside of a steps block into its individual :::step blocks.
     *
     * @param  array<int, string>  $inner
     * @return array<int, array{title: string|null, lines: array<int, string>}>
     */
    private function splitSteps(array $inner): array
    {
        $steps = [];
        $count = count($inner);

        for ($i = 0; $i < $count; $i++) {
            $trimmed = rtrim($inner[$i]);

            if (preg_match('/^(:{3,})step(.*)$/', $trimmed, $matches)) {
                $title = $this->attribute(trim($matches[2]), 'title');
                $lines = $this->consumeBlock($inner, $i, $matches[1]);
                $steps[] = ['title' => $title, 'lines' => $lines];
            }
        }

        return $steps;
    }

    private function attribute(string $attributes, string $key): ?string
    {
        return preg_match('/'.preg_quote($key, '/').'="([^"]*)"/', $attributes, $matches)
            ? $matches[1]
            : null;
    }

    /**
     * Render a run of plain Markdown lines.
     *
     * @param  array<int, string>  $lines
     */
    private function flush(array $lines): string
    {
        $markdown = trim(implode("\n", $lines));

        if ($markdown === '') {
            return '';
        }

        return $this->markdown->convert($markdown)->getContent();
    }

    /**
     * Give every h2 and h3 an id and collect them into the table of contents.
     *
     * @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>}
     */
    private function extractTableOfContents(string $html): array
    {
        $toc = [];
        $seen = [];

        $html = preg_replace_callback('/<h([23])>(.*?)<\/h\1>/s', function (array $matches) use (&$toc, &$seen): string {
            $level = (int) $matches[1];
            $text = trim(strip_tags($matches[2]));
            $id = Str::slug($text) ?: 'section';

            $base = $id;
            $suffix = 2;

            while (isset($seen[$id])) {
                $id = $base.'-'.$suffix++;
            }

            $seen[$id] = true;
            $toc[] = ['id' => $id, 'text' => $text, 'level' => $level];

            return '<h'.$level.' id="'.$id.'" class="doc-heading scroll-mt-36">'.$matches[2].'</h'.$level.'>';
        }, $html);

        return ['html' => $html, 'toc' => $toc];
    }
}
