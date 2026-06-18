<?php

declare(strict_types=1);

namespace App\Markdown\Parser;

use App\Markdown\Node\DocsDirective;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

class DocsDirectiveParser extends AbstractBlockContinueParser
{
    /**
     * @param  array<string, bool|string>  $options
     */
    public function __construct(
        string $name,
        array $options,
    ) {
        $this->block = new DocsDirective($name, $options);
    }

    private readonly DocsDirective $block;

    public function getBlock(): DocsDirective
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain(AbstractBlock $childBlock): bool
    {
        return true;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        $originalState = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrTab();

        if ($cursor->getRemainder() === ":::/{$this->block->name}") {
            return BlockContinue::finished();
        }

        $cursor->restoreState($originalState);

        return BlockContinue::at($cursor);
    }
}
