<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\UpdateSupportTicketAsInstanceAdmin;
use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportController extends Controller
{
    /**
     * The support inbox, spanning every account on the instance. The tab lives in
     * the URL so each bucket is its own page, and opening the section without a
     * tab lands on the open one. The open bucket holds everything that is not
     * closed, so a conversation the team has answered stays in view until it is
     * closed for good.
     */
    public function index(Request $request, string $status = 'open', ?int $ticket = null): View
    {
        $search = trim((string) $request->query('search', ''));

        $tickets = SupportTicket::query()
            ->with('user.account')
            ->withCount('messages')
            ->when($status === 'open', fn ($query) => $query->where('status', '!=', SupportTicketStatus::Closed))
            ->when($status === 'closed', fn ($query) => $query->where('status', SupportTicketStatus::Closed))
            ->latest()
            ->latest('id')
            ->get()
            ->pipe(fn ($tickets) => $this->filterBySearch($tickets, $search));

        return view('app.instance.support.index', [
            'status' => $status,
            'search' => $search,
            'tickets' => $tickets,
            'selected' => $this->resolveSelected($ticket, $tickets),
            'openCount' => SupportTicket::query()->where('status', '!=', SupportTicketStatus::Closed)->count(),
            'closedCount' => SupportTicket::query()->where('status', SupportTicketStatus::Closed)->count(),
            'allCount' => SupportTicket::query()->count(),
        ]);
    }

    /**
     * Narrow the inbox to a free-text search. The subject, requester and account
     * name are all encrypted at rest, so the match happens in memory over the
     * already-loaded collection rather than in SQL.
     *
     * @param  Collection<int, SupportTicket>  $tickets
     * @return Collection<int, SupportTicket>
     */
    private function filterBySearch(Collection $tickets, string $search): Collection
    {
        if ($search === '') {
            return $tickets;
        }

        $needle = mb_strtolower($search);

        return $tickets->filter(function (SupportTicket $ticket) use ($needle): bool {
            $haystack = mb_strtolower(implode(' ', [
                $ticket->subject,
                $ticket->user->getFullName(),
                $ticket->user->account->name,
            ]));

            return str_contains($haystack, $needle);
        })->values();
    }

    /**
     * Close or reopen a conversation from the panel. The redirect drops the admin
     * back on the tab the ticket now lives under, with it still selected.
     */
    public function update(Request $request, int $supportTicket): RedirectResponse
    {
        $ticket = SupportTicket::query()->findOrFail($supportTicket);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'closed'])],
        ]);

        $status = SupportTicketStatus::from($validated['status']);

        new UpdateSupportTicketAsInstanceAdmin(
            user: $request->user(),
            ticket: $ticket,
            status: $status,
        )->execute();

        $tab = $status === SupportTicketStatus::Closed ? 'closed' : 'open';

        return to_route('instanceAdmin.support.index', ['status' => $tab, 'ticket' => $ticket->id])
            ->with('status', $status === SupportTicketStatus::Closed ? 'Conversation closed' : 'Conversation reopened');
    }

    /**
     * The conversation shown in the detail pane. A ticket named in the path wins
     * so a conversation can be linked to directly; otherwise the first of the list
     * opens, so the pane is never empty while there is something to read.
     *
     * @param  Collection<int, SupportTicket>  $tickets
     */
    private function resolveSelected(?int $ticket, Collection $tickets): ?SupportTicket
    {
        $id = $ticket ?? $tickets->first()?->id;

        if ($id === null) {
            return null;
        }

        return SupportTicket::query()
            ->with(['user', 'messages.user'])
            ->find($id);
    }
}
