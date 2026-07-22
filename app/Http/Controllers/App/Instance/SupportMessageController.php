<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\CreateSupportTeamMessage;
use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportMessageController extends Controller
{
    public function create(Request $request, int $supportTicket): RedirectResponse
    {
        // The inbox spans every account, so the ticket is looked up unscoped. The
        // action gates on the instance administration flag, the same way the rest
        // of the panel does.
        $ticket = SupportTicket::query()->findOrFail($supportTicket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        new CreateSupportTeamMessage(
            user: $request->user(),
            ticket: $ticket,
            body: $validated['body'],
        )->execute();

        return to_route('instanceAdmin.support.index', ['status' => $this->tabFor($ticket), 'ticket' => $ticket->id])
            ->with('status', 'Reply sent');
    }

    /**
     * The tab the ticket now lives under, so the redirect keeps it in view.
     */
    private function tabFor(SupportTicket $ticket): string
    {
        return $ticket->status === SupportTicketStatus::Closed ? 'closed' : 'open';
    }
}
