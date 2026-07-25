<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\SearchableEnum;

/**
 * One row on the search screen.
 *
 * This is the shared shape every searchable model maps into, so the screen and
 * the API can render an item, a loan and a document side by side without
 * knowing anything about any of them. Nothing here is persisted: a result is
 * assembled at read time from the record it points at.
 *
 * The score is the weight of the weakest field the query matched in, so a
 * result scoring 100 is one where every word typed appeared in the name.
 */
class SearchResult
{
    public function __construct(
        public readonly SearchableEnum $type,
        public readonly int $id,
        public readonly string $title,
        public readonly string $context,
        public readonly ?string $collectionName,
        public readonly string $url,
        public readonly ?string $thumbnailUrl,
        public readonly int $score,
    ) {}

    /**
     * Whether every word typed matched the name of the record rather than
     * something further out, which is what the row says next to its link.
     */
    public function isTitleMatch(): bool
    {
        return $this->score >= 100;
    }
}
