<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\IndexSearchable;
use App\Enums\SearchableEnum;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

/**
 * Rebuilds the account wide search index from the records themselves.
 *
 * Safe to run again at any time. It is how an existing installation fills the
 * index after upgrading, since a migration can create the table but not the
 * hashes, and how the index is repaired if it ever drifts behind a rename.
 */
class RebuildSearchIndexCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'search:rebuild-index
        {--type= : Rebuild one kind of record only, e.g. item, collection, copy}';

    /**
     * @var string
     */
    protected $description = 'Rebuild the account wide search index';

    public function handle(): int
    {
        $types = $this->types();

        if ($types === []) {
            $this->error('Unknown type. Pick one of: '.implode(', ', array_column(SearchableEnum::cases(), 'value')).'.');

            return self::FAILURE;
        }

        $indexed = 0;

        foreach ($types as $type) {
            $this->line('Indexing '.$type->pluralLabel().'…');

            $indexed += $this->rebuild($type);
        }

        $this->info($indexed.' record(s) indexed.');

        return self::SUCCESS;
    }

    /**
     * @return list<SearchableEnum>
     */
    private function types(): array
    {
        $wanted = $this->option('type');

        if ($wanted === null) {
            return SearchableEnum::cases();
        }

        $type = SearchableEnum::tryFrom((string) $wanted);

        return $type === null ? [] : [$type];
    }

    private function rebuild(SearchableEnum $type): int
    {
        $count = 0;

        $type->modelClass()::query()
            ->with($type->relations())
            ->chunkById(100, function (Collection $records) use (&$count): void {
                foreach ($records as $record) {
                    new IndexSearchable(searchable: $record)->execute();
                    $count++;
                }
            });

        return $count;
    }
}
