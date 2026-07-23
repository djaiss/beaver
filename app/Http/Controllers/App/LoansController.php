<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Copy;
use App\Models\Loan;
use App\Services\LoanDashboard;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The account-wide Loans section: an operational dashboard over custody workflows.
 *
 * A loan moves custody, not ownership, so this reads the account's loans in one
 * direction at a time, lent out or borrowed in, and slices them into the stat
 * tiles and tabs the section shows. Reading is open to any role; the create,
 * return, edit and delete actions run through the existing copy-scoped
 * LoanController, which already gates on the owner or editor role.
 *
 * Direction, the open tab and the selected loan all live in the URL path so every
 * view is its own address; only the filters and the search use the query string.
 */
class LoansController extends Controller
{
    /**
     * The tabs, in the order they appear.
     */
    private const array TABS = ['all', 'due', 'risk', 'by-party', 'deposits', 'timeline'];

    /**
     * Land on the outgoing all-loans view.
     */
    public function index(): RedirectResponse
    {
        return to_route('loans.show', ['direction' => LoanDirection::Outgoing->slug(), 'tab' => 'all']);
    }

    /**
     * Show a direction and tab, with no drawer open.
     */
    public function show(Request $request, string $direction, string $tab = 'all'): View
    {
        return $this->render($request, LoanDirection::fromSlug($direction), $tab);
    }

    /**
     * Show a direction and tab with a loan's detail drawer open over it.
     */
    public function detail(Request $request, string $direction, string $tab, int $loan): View
    {
        $loanDirection = LoanDirection::fromSlug($direction);

        return $this->render($request, $loanDirection, $tab, $this->findLoan($request, $loanDirection, $loan));
    }

    /**
     * Show a direction with the create-loan drawer open over the all-loans tab.
     */
    public function new(Request $request, string $direction): View
    {
        return $this->render($request, LoanDirection::fromSlug($direction), 'all', showCreate: true);
    }

    /**
     * Download a "what is currently out" report for the direction as CSV.
     */
    public function export(Request $request, string $direction): StreamedResponse
    {
        $loanDirection = LoanDirection::fromSlug($direction);
        $dashboard = new LoanDashboard($request->user()->account, $loanDirection);

        $rows = $dashboard->filtered(status: 'open');

        $filename = 'loans-'.$loanDirection->slug().'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'wb');

            fputcsv($handle, ['Item', 'Copy', 'Collection', 'Party', 'Status', 'Loaned on', 'Due', 'Condition out']);

            foreach ($rows as $loan) {
                fputcsv($handle, [
                    $loan->copy->item->name,
                    $loan->copy->identifier ?? '',
                    $loan->copy->item->collection->name,
                    $loan->party,
                    $loan->status->label(),
                    $loan->loaned_at?->format('Y-m-d') ?? '',
                    $loan->due_at?->format('Y-m-d') ?? '',
                    $loan->itemConditionOut?->name ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Build the whole section for a direction and tab, optionally with a drawer.
     */
    private function render(Request $request, LoanDirection $direction, string $tab, ?Loan $selectedLoan = null, bool $showCreate = false): View
    {
        if (! in_array($tab, self::TABS, true)) {
            abort(404);
        }

        $account = $request->user()->account;
        $dashboard = new LoanDashboard($account, $direction);
        $filters = $this->filters($request);
        $stats = $dashboard->statTiles();

        return view('app.loans.index', [
            'direction' => $direction,
            'tab' => $tab,
            'stats' => $stats,
            'filters' => $filters,
            'filterCollections' => $dashboard->collections(),
            'tabData' => $this->tabData($dashboard, $tab, $filters),
            'selectedLoan' => $selectedLoan,
            'showCreate' => $showCreate,
            'createCatalog' => $this->createCatalog($account),
            'conditions' => $account->itemConditions->pluck('name', 'id'),
            'currencies' => $this->currencyOptions(),
        ]);
    }

    /**
     * The data the active tab needs, so the view only asks the dashboard for what
     * it renders.
     *
     * @param  array{search: ?string, collection: ?int, status: ?string, sort: string}  $filters
     * @return array<string, mixed>
     */
    private function tabData(LoanDashboard $dashboard, string $tab, array $filters): array
    {
        return match ($tab) {
            'due' => $dashboard->dueGroups(),
            'risk' => $dashboard->riskGroups(),
            'by-party' => ['parties' => $dashboard->parties($filters['search'], $filters['collection'])],
            'deposits' => $dashboard->depositsData(),
            'timeline' => $dashboard->timelineData(),
            default => ['loans' => $dashboard->filtered($filters['search'], $filters['collection'], $filters['status'], $filters['sort'])],
        };
    }

    /**
     * Read the filter bar from the query string. These are refinements of a list,
     * like search and pagination, so they belong in the query string rather than
     * the path.
     *
     * @return array{search: ?string, collection: ?int, status: ?string, sort: string}
     */
    private function filters(Request $request): array
    {
        $collection = $request->query('collection');
        $status = $request->query('status');

        return [
            'search' => $request->query('search'),
            'collection' => is_numeric($collection) ? (int) $collection : null,
            'status' => is_string($status) && $status !== '' ? $status : null,
            'sort' => is_string($request->query('sort')) ? $request->query('sort') : 'due',
        ];
    }

    /**
     * Resolve a loan in this account and direction, or 404. Loans have no account
     * column, so a cross-account or wrong-direction id simply is not found.
     */
    private function findLoan(Request $request, LoanDirection $direction, int $loanId): Loan
    {
        try {
            return Loan::query()
                ->forAccount($request->user()->account)
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
                ->findOrFail($loanId);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * The collection → item → copy catalog the create drawer walks, with each copy
     * flagged when it already has an open outgoing loan so the overlap block can
     * show before the form is even submitted.
     *
     * @return list<array{id: int, name: string, items: list<array{id: int, name: string, copies: list<array{id: int, label: string, openOut: bool}>}>}>
     */
    private function createCatalog(Account $account): array
    {
        $collections = $account->collections()
            ->with(['items.copies' => fn ($query) => $query->with(['itemCondition', 'loans' => fn ($loans) => $loans->where('direction', LoanDirection::Outgoing)->whereIn('status', LoanStatus::openCases())])])
            ->orderBy('name')
            ->get();

        return $collections->map(fn ($collection): array => [
            'id' => $collection->id,
            'name' => $collection->name,
            'items' => $collection->items->map(fn ($item): array => [
                'id' => $item->id,
                'name' => $item->name,
                'copies' => $item->copies->map(fn ($copy): array => [
                    'id' => $copy->id,
                    'label' => $this->copyLabel($copy),
                    'openOut' => $copy->loans->isNotEmpty(),
                ])->values()->all(),
            ])->values()->all(),
        ])->values()->all();
    }

    private function copyLabel(Copy $copy): string
    {
        $label = $copy->identifier ?? '#'.$copy->id;

        if ($copy->itemCondition !== null) {
            $label .= ' · '.$copy->itemCondition->name;
        }

        return $label;
    }

    /**
     * The currencies a deposit can be recorded in, flag first.
     *
     * @return array<string, string>
     */
    private function currencyOptions(): array
    {
        return collect(config('currencies'))
            ->map(fn (array $currency, string $code): string => $currency['flag'].' '.$code)
            ->all();
    }
}
