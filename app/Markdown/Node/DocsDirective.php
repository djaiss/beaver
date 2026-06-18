<?php

declare(strict_types=1);

namespace App\Markdown\Node;

use League\CommonMark\Node\Block\AbstractBlock;

class DocsDirective extends AbstractBlock
{
    /**
     * @param  array<string, bool|string>  $options
     */
    public function __construct(
        public readonly string $name,
        public readonly array $options,
    ) {
        parent::__construct();
    }
}
