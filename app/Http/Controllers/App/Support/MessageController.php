<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Support;

use App\Actions\CreateSupportMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function create(Request $request, int $supportTicket): RedirectResponse
    {
        $ticket = $request->user()
            ->supportTickets()
            ->findOrFail($supportTicket);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        new CreateSupportMessage(
            user: $request->user(),
            ticket: $ticket,
            body: $validated['body'],
        )->execute();

        return to_route('support.tickets.show', $ticket)
            ->with('status', trans('Reply sent'));
    }
}
