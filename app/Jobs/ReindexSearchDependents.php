<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\IndexSearchable;
use App\Contracts\Searchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Rebuild the search hashes of every record that quotes this one.
 *
 * An item is searchable by the name of its collection as well as by its own, so
 * renaming a collection leaves its items indexed under the old name until this
 * runs. Which records those are is the renamed record's own answer, through
 * searchableDependents().
 */
class ReindexSearchDependents implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Model&Searchable $searchable,
    ) {}

    public function handle(): void
    {
        foreach ($this->searchable->searchableDependents() as $dependent) {
            new IndexSearchable(searchable: $dependent)->execute();
        }
    }
}
