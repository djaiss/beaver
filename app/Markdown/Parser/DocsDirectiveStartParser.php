<?php

declare(strict_types=1);

namespace App\Markdown\Parser;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

class DocsDirectiveStartParser implements BlockStartParserInterface
{
    public function __construct(private readonly string $name) {}

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented() || $parserState->getParagraphContent() !== null || ! $parserState->getActiveBlockParser()->isContainer()) {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();

        $pattern = '/^:::'.preg_quote($this->name, '/').'(?:\s+(.*?))?\s*$/';

        if (preg_match($pattern, $cursor->getRemainder(), $matches) !== 1) {
            return BlockStart::none();
        }

        $cursor->advanceToEnd();

        return BlockStart::of(
            new DocsDirectiveParser($this->name, $this->parseOptions($matches[1] ?? '')),
        )->at($cursor);
    }

    /**
     * @return array<string, bool|string>
     */
    private function parseOptions(string $input): array
    {
        preg_match_all(
            '/([a-z][a-z0-9-]*)(?:=(?:"([^"]*)"|\'([^\']*)\'|([^\s]+)))?/i',
            $input,
            $matches,
            PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL,
        );

        $options = [];

        foreach ($matches as $match) {
            $value = $match[2] ?? $match[3] ?? $match[4] ?? true;

            $options[$match[1]] = $value;
        }

        return $options;
    }
}
