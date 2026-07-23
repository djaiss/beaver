<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Account;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Assembles the account-wide view of loans in one direction, lent out or borrowed
 * in, for the Loans section.
 *
 * Like BuildCopyHistory this is a read model, not a table: loans stay the source
 * of truth and everything here, the stat tiles, the due and risk buckets, the
 * party grouping, the deposit totals and the timeline, is derived on the fly. The
 * account's loans in the chosen direction are loaded once with their relations, so
 * the whole dashboard is a bounded number of queries. Party, copy identifier and
 * condition names are encrypted, so searching, grouping and sorting on them happen
 * in PHP rather than SQL, which is fine at the scale of one collector's loans.
 */
class LoanDashboard
{
    /**
     * The account's loans in this direction, eager loaded.
     *
     * @var Collection<int, Loan>
     */
    private Collection $loans;

    /**
     * How many days ahead still counts as "due soon".
     */
    private const int DUE_SOON_DAYS = 30;

    public function __construct(
        private readonly Account $account,
        private readonly LoanDirection $direction,
    ) {
        $this->loans = Loan::query()
            ->forAccount($account)
            ->where('direction', $direction)
            ->with([
                'copy.item.collection',
                'copy.item.mainPhoto',
                'itemConditionOut',
                'itemConditionIn',
                'documents',
                'loanProvenanceEvent',
                'returnProvenanceEvent',
            ])
            ->get();
    }

    public function direction(): LoanDirection
    {
        return $this->direction;
    }

    /**
     * The clickable KPI numbers at the top of the section.
     *
     * @return array{active: int, planned: int, dueSoon: int, overdue: int, returned: int, deposits: array<string, int>}
     */
    public function statTiles(): array
    {
        return [
            'active' => $this->loans->where('status', LoanStatus::Active)->count(),
            'planned' => $this->loans->where('status', LoanStatus::Planned)->count(),
            'dueSoon' => $this->dueSoon()->count(),
            'overdue' => $this->overdue()->count(),
            'returned' => $this->loans->where('status', LoanStatus::Returned)->count(),
            'deposits' => $this->openDepositTotals(),
        ];
    }

    /**
     * The All loans tab: every loan in this direction, narrowed by the filters and
     * ordered by the chosen sort.
     *
     * @return Collection<int, Loan>
     */
    public function filtered(?string $search = null, ?int $collectionId = null, ?string $status = null, string $sort = 'due'): Collection
    {
        $loans = $this->loans;

        if ($collectionId !== null) {
            $loans = $loans->filter(fn (Loan $loan): bool => $loan->copy->item->collection->id === $collectionId);
        }

        if ($status !== null) {
            $loans = $this->filterByStatus($loans, $status);
        }

        if ($search !== null && trim($search) !== '') {
            $loans = $this->filterBySearch($loans, $search);
        }

        return $this->sort($loans, $sort)->values();
    }

    /**
     * The Due & overdue tab, split into overdue, due soon, and open ended groups.
     *
     * @return array{overdue: Collection<int, Loan>, dueSoon: Collection<int, Loan>, openEnded: Collection<int, Loan>}
     */
    public function dueGroups(): array
    {
        return [
            'overdue' => $this->overdue()->values(),
            'dueSoon' => $this->dueSoon()->values(),
            'openEnded' => $this->loans->filter(fn (Loan $loan): bool => $loan->isOpenEnded())->values(),
        ];
    }

    /**
     * The Risk & exceptions tab: the loans that need a second look.
     *
     * @return array{overdue: Collection<int, Loan>, lost: Collection<int, Loan>, returnedWorse: Collection<int, Loan>, noDueDate: Collection<int, Loan>, missingConditionOut: Collection<int, Loan>, activeNoDocuments: Collection<int, Loan>}
     */
    public function riskGroups(): array
    {
        return [
            'overdue' => $this->overdue()->values(),
            'lost' => $this->loans->where('status', LoanStatus::Lost)->values(),
            'returnedWorse' => $this->loans->filter(fn (Loan $loan): bool => $loan->returnedWorse())->values(),
            'noDueDate' => $this->loans->filter(fn (Loan $loan): bool => $loan->isOpenEnded())->values(),
            'missingConditionOut' => $this->loans
                ->filter(fn (Loan $loan): bool => $loan->status->isOpen() && $loan->item_condition_out_id === null)
                ->values(),
            'activeNoDocuments' => $this->loans
                ->filter(fn (Loan $loan): bool => $loan->status->hasLeftCustody() && $loan->documents->isEmpty())
                ->values(),
        ];
    }

    /**
     * The By party tab: one group per party, most active first.
     *
     * @return Collection<int, array{name: string, active: int, loans: Collection<int, Loan>}>
     */
    public function parties(?string $search = null, ?int $collectionId = null): Collection
    {
        $loans = $this->loans;

        if ($collectionId !== null) {
            $loans = $loans->filter(fn (Loan $loan): bool => $loan->copy->item->collection->id === $collectionId);
        }

        if ($search !== null && trim($search) !== '') {
            $loans = $this->filterBySearch($loans, $search);
        }

        return $loans
            ->groupBy(fn (Loan $loan): string => $loan->party)
            ->map(fn (Collection $group, string $party): array => [
                'name' => $party,
                'active' => $group->filter(fn (Loan $loan): bool => $loan->status->hasLeftCustody())->count(),
                'loans' => $group->sortByDesc('loaned_at')->values(),
            ])
            ->sortByDesc('active')
            ->values();
    }

    /**
     * The Deposits tab: totals held or owed across open loans, plus the loans that
     * carry a deposit.
     *
     * @return array{totals: array<string, int>, count: int, loans: Collection<int, Loan>}
     */
    public function depositsData(): array
    {
        $withDeposit = $this->loans->filter(fn (Loan $loan): bool => $loan->deposit_amount !== null);

        return [
            'totals' => $this->openDepositTotals(),
            'count' => $withDeposit->count(),
            'loans' => $withDeposit->sortByDesc('loaned_at')->values(),
        ];
    }

    /**
     * The Timeline tab: open loans by due date, plus the most recent returns and
     * the most recent departures.
     *
     * @return array{upcoming: Collection<int, Loan>, recentlyReturned: Collection<int, Loan>, recentlyLoaned: Collection<int, Loan>}
     */
    public function timelineData(): array
    {
        return [
            'upcoming' => $this->loans
                ->filter(fn (Loan $loan): bool => $loan->status->isOpen() && $loan->due_at !== null)
                ->sortBy('due_at')
                ->values(),
            'recentlyReturned' => $this->loans
                ->where('status', LoanStatus::Returned)
                ->sortByDesc('returned_at')
                ->take(8)
                ->values(),
            'recentlyLoaned' => $this->loans
                ->sortByDesc('loaned_at')
                ->take(5)
                ->values(),
        ];
    }

    /**
     * The distinct collections that have a loan in this direction, for the filter
     * dropdown. Keyed by id, valued by name.
     *
     * @return array<int, string>
     */
    public function collections(): array
    {
        return $this->loans
            ->map(fn (Loan $loan): object => $loan->copy->item->collection)
            ->unique('id')
            ->sortBy('name')
            ->mapWithKeys(fn (object $collection): array => [$collection->id => $collection->name])
            ->all();
    }

    /**
     * The loans that are out and past their due date. A loan is overdue when it
     * carries the stored Overdue status, or when it is still out and its due date
     * has slipped past between scheduled overdue checks.
     *
     * @return Collection<int, Loan>
     */
    private function overdue(): Collection
    {
        return $this->loans->filter(fn (Loan $loan): bool => $loan->isEffectivelyOverdue());
    }

    /**
     * The loans that are out and fall due within the next month.
     *
     * @return Collection<int, Loan>
     */
    private function dueSoon(): Collection
    {
        $today = Carbon::today();
        $window = $today->copy()->addDays(self::DUE_SOON_DAYS);

        return $this->loans->filter(function (Loan $loan) use ($today, $window): bool {
            if (! $loan->status->hasLeftCustody() || $loan->due_at === null) {
                return false;
            }

            return $loan->due_at->betweenIncluded($today, $window);
        });
    }

    private function isEffectivelyOverdue(Loan $loan): bool
    {
        if ($loan->status === LoanStatus::Overdue) {
            return true;
        }

        return $loan->status->hasLeftCustody()
            && $loan->due_at !== null
            && $loan->due_at->isBefore(Carbon::today());
    }

    /**
     * Sum the open-loan deposits by currency, so a section can show a total held or
     * owed even when loans mix currencies.
     *
     * @return array<string, int>
     */
    private function openDepositTotals(): array
    {
        return $this->loans
            ->filter(fn (Loan $loan): bool => $loan->status->isOpen() && $loan->deposit_amount !== null)
            ->groupBy('deposit_currency_code')
            ->map(fn (Collection $group): int => (int) $group->sum('deposit_amount'))
            ->all();
    }

    /**
     * @param  Collection<int, Loan>  $loans
     * @return Collection<int, Loan>
     */
    private function filterByStatus(Collection $loans, string $status): Collection
    {
        return match ($status) {
            'overdue' => $loans->filter(fn (Loan $loan): bool => $this->isEffectivelyOverdue($loan)),
            'due-soon' => $this->dueSoon(),
            'open' => $loans->filter(fn (Loan $loan): bool => $loan->status->isOpen()),
            default => $loans->filter(fn (Loan $loan): bool => $loan->status->value === $status),
        };
    }

    /**
     * Match the search against the party, the item name and the copy identifier,
     * all of which are encrypted and so compared in PHP.
     *
     * @param  Collection<int, Loan>  $loans
     * @return Collection<int, Loan>
     */
    private function filterBySearch(Collection $loans, string $search): Collection
    {
        $needle = Str::lower(trim($search));

        return $loans->filter(function (Loan $loan) use ($needle): bool {
            $haystack = Str::lower(implode(' ', array_filter([
                $loan->party,
                $loan->copy->item->name,
                $loan->copy->identifier,
            ])));

            return Str::contains($haystack, $needle);
        });
    }

    /**
     * @param  Collection<int, Loan>  $loans
     * @return Collection<int, Loan>
     */
    private function sort(Collection $loans, string $sort): Collection
    {
        return match ($sort) {
            'recent' => $loans->sortByDesc('loaned_at'),
            'party' => $loans->sortBy(fn (Loan $loan): string => Str::lower($loan->party)),
            // Due date first, open-ended loans last, so what needs attention rises.
            default => $loans->sortBy(fn (Loan $loan): string => $loan->due_at?->format('Y-m-d') ?? '9999-12-31'),
        };
    }
}
