<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A record that account wide search can find.
 *
 * The first three methods are what the index is built from. The rest are what a
 * result row shows: they live on the model rather than in the search service so
 * that adding a searchable kind of record means writing one model, not editing
 * one long match statement.
 *
 * App\Traits\HasSearchIndex supplies the wiring and a default for the three
 * optional ones.
 */
interface Searchable
{
    /**
     * Get the search hashes of the record.
     */
    public function searchTokens(): MorphMany;

    /**
     * The column whose change makes the records quoting this one go stale.
     */
    public function searchableTitleColumn(): string;

    /**
     * The account the record belongs to, or null while it cannot be resolved
     * yet, which is how a half built record is left out of the index.
     */
    public function searchableAccountId(): ?int;

    /**
     * The text worth indexing, keyed by how strong a match on it counts as. The
     * weights are the ones in App\Actions\IndexSearchable.
     *
     * @return array<int, list<string>>
     */
    public function searchableText(): array;

    /**
     * The heading of the result row.
     */
    public function searchableTitle(): string;

    /**
     * The one line under the heading, saying what the record is.
     */
    public function searchableContext(): string;

    /**
     * The screen the result row opens. It has to be a screen every role may
     * open, so nobody is sent to a 404 by their own search.
     */
    public function searchableUrl(): string;

    /**
     * The thumbnail of the result row, when the record has a picture.
     */
    public function searchableThumbnailUrl(): ?string;

    /**
     * The collection the record sits in, shown beside the heading. Null for the
     * records that belong to the whole account.
     */
    public function searchableCollectionName(): ?string;

    /**
     * The records whose indexed text quotes this one, and so go stale when it
     * is renamed.
     *
     * @return iterable<int, Model>
     */
    public function searchableDependents(): iterable;
}
