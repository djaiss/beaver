<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\LoanDirection;
use App\Http\Controllers\Controller;
use App\Services\LoanDashboard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The "what is currently out" report for the Loans section, streamed as CSV.
 *
 * It sits in its own controller because it is not part of the loans resource: it
 * renders the open loans of a direction as a downloadable file rather than a page.
 * Reading it is open to any role, the same as the section it comes from.
 */
class LoanExportController extends Controller
{
    public function show(Request $request, string $direction): StreamedResponse
    {
        $loanDirection = LoanDirection::fromSlug($direction);
        $rows = new LoanDashboard($request->user()->account, $loanDirection)->filtered(status: 'open');

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
                    $loan->item_condition_out_id === null ? '' : $loan->itemConditionOut->name,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
