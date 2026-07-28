<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\CreateCatalog;
use App\Actions\CreateCategory;
use App\Actions\CreateItem;
use App\Actions\CreateSet;
use App\Actions\CreateTransaction;
use App\Enums\CopyStatus;
use App\Enums\TransactionType;
use App\Enums\VisibilityEnum;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Location;
use App\Models\Set;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Seeder;

/**
 * A collection with enough shape to it that every screen has something to show,
 * the statistics one in particular.
 *
 * That screen reads almost everything from the copies rather than the items, so
 * the copies here are what carry the demo: an acquisition date spread over two
 * years so the charts have a curve, a value so the totals and the rankings mean
 * something, and a condition and a location so the breakdowns are not one bar.
 *
 * The category sizes are deliberately lopsided, and there are more of them than
 * the donut names, so the "other categories" slice shows up too.
 */
class CatalogSeeder extends Seeder
{
    /**
     * Picks a bucket more often the lower its index, so the condition and the
     * location breakdowns come out uneven the way a real shelf is.
     *
     * @var list<int>
     */
    private const array WEIGHTS = [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 4];

    public function run(): void
    {
        $user = User::query()->where('email', 'admin@admin.com')->firstOrFail();
        $account = $user->account;

        $comics = $account->catalogTypes()->get()->firstWhere('name', 'Comics');

        $catalog = new CreateCatalog(
            user: $user,
            account: $account,
            name: 'Marvel Comics 1990s',
            description: 'Key issues and full runs from the 1990s Marvel era. Spider-Man, X-Men, and Infinity saga tie-ins.',
            emoji: '📚',
            visibility: VisibilityEnum::Shared->value,
            currency: 'USD',
            catalogTypeIds: $comics === null ? [] : [$comics->id],
        )->execute();

        $categories = $this->createCategories($user, $catalog);
        $sets = $this->createSets($user, $catalog);

        $conditions = $account->itemConditions()->get();
        $locations = $account->locations()->get();

        $this->createItems($user, $catalog, $categories, $sets, $conditions, $locations);

        $this->command->info('Seeded the "Marvel Comics 1990s" collection.');
    }

    /**
     * @return array<string, Category>
     */
    private function createCategories(User $user, Catalog $catalog): array
    {
        $categories = [];

        foreach (['Spider-Man', 'X-Men', 'Infinity Saga', 'Wolverine', 'Fantastic Four', 'Avengers', 'Daredevil', 'Silver Surfer'] as $name) {
            $categories[$name] = new CreateCategory(
                user: $user,
                catalog: $catalog,
                name: $name,
            )->execute();
        }

        return $categories;
    }

    /**
     * Both sets target more issues than the collection actually holds, so the
     * completion figure lands part way rather than at a flat 100%.
     *
     * @return array<string, Set>
     */
    private function createSets(User $user, Catalog $catalog): array
    {
        $sets = [];

        foreach (['Amazing Spider-Man #300-330' => 31, 'X-Men #1-20 (1991)' => 20] as $name => $target) {
            $sets[$name] = new CreateSet(
                user: $user,
                catalog: $catalog,
                name: $name,
                targetCount: $target,
            )->execute();
        }

        return $sets;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array<string, Set>  $sets
     * @param  EloquentCollection<int, ItemCondition>  $conditions
     * @param  EloquentCollection<int, Location>  $locations
     */
    private function createItems(User $user, Catalog $catalog, array $categories, array $sets, EloquentCollection $conditions, EloquentCollection $locations): void
    {
        $this->command->info('Creating the items of the collection...');

        $counter = 0;

        foreach ($this->itemPlan() as [$title, $categoryName, $issueFrom, $issueTo, $lowValue, $highValue]) {
            foreach (range($issueFrom, $issueTo) as $issue) {
                $counter++;

                $set = null;

                if ($categoryName === 'Spider-Man' && $issue >= 300 && $issue <= 320) {
                    $set = $sets['Amazing Spider-Man #300-330'];
                }

                if ($categoryName === 'X-Men' && $title === 'X-Men' && $issue <= 12) {
                    $set = $sets['X-Men #1-20 (1991)'];
                }

                $copies = $this->copiesFor($counter, $lowValue, $highValue, $conditions, $locations);

                $item = new CreateItem(
                    user: $user,
                    catalog: $catalog,
                    name: $title.' #'.$issue,
                    category: $categories[$categoryName],
                    set: $set,
                    copies: $copies,
                )->execute();

                $this->recordAcquisitions($user, $item, $copies);
                $this->backdate($item, $copies);
            }
        }

        $this->command->info('Created '.$counter.' items.');
    }

    /**
     * Record how each copy was acquired.
     *
     * The acquisition date and the purchase price are not columns on a copy:
     * they are read from the transaction that brought it in. Seeding those
     * transactions is what gives the statistics screen its two charts over time,
     * which would otherwise sit flat however much the collection holds.
     *
     * Every eleventh copy is left without one, so the screen has a reason to say
     * that some copies have no acquisition recorded.
     *
     * @param  list<array{backdate_to: string|null, estimated_value: int, ...}>  $copies
     */
    private function recordAcquisitions(User $user, Item $item, array $copies): void
    {
        foreach ($item->copies as $index => $copy) {
            $seeded = $copies[$index] ?? null;

            if ($seeded === null || $seeded['backdate_to'] === null) {
                continue;
            }

            new CreateTransaction(
                user: $user,
                copy: $copy,
                type: TransactionType::Purchase,
                occurredAt: $seeded['backdate_to'],
                amount: (int) round($seeded['estimated_value'] * 0.6),
            )->execute();
        }
    }

    /**
     * An item enters the collection when its first copy was acquired, not when
     * the seeder happened to run. Without this every item reads as added today,
     * and the "added this month" figure claims the whole collection.
     *
     * @param  list<array{backdate_to: string|null, ...}>  $copies
     */
    private function backdate(Item $item, array $copies): void
    {
        $dates = array_filter(array_column($copies, 'backdate_to'));

        if ($dates === []) {
            return;
        }

        Item::query()->where('id', $item->id)->update(['created_at' => min($dates), 'updated_at' => min($dates)]);
    }

    /**
     * The copies of one item. Most items are owned once, and every seventh is
     * owned twice.
     *
     * The conditions and the locations are drawn from a weighted table rather
     * than cycled through, otherwise every bar of those two breakdowns comes out
     * the same length and the charts say nothing.
     *
     * `backdate_to` is not a column on a copy. It is when this seed pretends the
     * item entered the collection, used only to backdate the item itself, and it
     * is ignored when the copy is written. When transactions land the real
     * acquisition date comes from them instead.
     *
     * @param  EloquentCollection<int, ItemCondition>  $conditions
     * @param  EloquentCollection<int, Location>  $locations
     * @return list<array{item_condition_id: int|null, current_location_id: int|null, status: CopyStatus, quantity: int, estimated_value: int, backdate_to: string|null}>
     */
    private function copiesFor(int $counter, int $lowValue, int $highValue, EloquentCollection $conditions, EloquentCollection $locations): array
    {
        $copies = [];
        $howMany = $counter % 7 === 0 ? 2 : 1;

        foreach (range(1, $howMany) as $index) {
            $seed = $counter * 13 + $index * 7;

            // Spread over the last two years and weighted towards the recent
            // months, so the acquisition chart climbs instead of trailing off.
            $monthsAgo = 23 - (int) floor(sqrt($seed % 576));

            $value = $lowValue + (($seed * 37) % max(1, $highValue - $lowValue));

            $copies[] = [
                'item_condition_id' => $conditions->isEmpty() ? null : $conditions[self::WEIGHTS[$seed % count(self::WEIGHTS)] % $conditions->count()]->id,
                'current_location_id' => $locations->isEmpty() ? null : $locations[self::WEIGHTS[($seed + 5) % count(self::WEIGHTS)] % $locations->count()]->id,
                'status' => CopyStatus::Owned,
                'quantity' => 1,
                'estimated_value' => $value,
                'backdate_to' => $counter % 11 === 0 ? null : Carbon::now()->subMonths($monthsAgo)->subDays($seed % 28)->format('Y-m-d'),
            ];
        }

        return $copies;
    }

    /**
     * Title, category, first issue, last issue, and the value range in cents the
     * copies of that run fall into.
     *
     * The plan stays under the free item allowance on purpose, so a freshly
     * seeded account on a hosted instance does not start life already at its
     * ceiling. It still spreads across several categories and both sets, so the
     * statistics, the set completion and the copy history all have something to
     * show. Add a run here if you need more, and expect the upgrade screens to
     * appear once the account passes ten items.
     *
     * @return list<array{0: string, 1: string, 2: int, 3: int, 4: int, 5: int}>
     */
    private function itemPlan(): array
    {
        return [
            ['Amazing Spider-Man', 'Spider-Man', 300, 302, 4000, 85000],
            ['Spider-Man', 'Spider-Man', 1, 1, 2000, 64000],
            ['X-Men', 'X-Men', 1, 1, 3000, 31000],
            ['Infinity Gauntlet', 'Infinity Saga', 1, 2, 3500, 22000],
            ['Wolverine', 'Wolverine', 40, 40, 1000, 8000],
        ];
    }
}
