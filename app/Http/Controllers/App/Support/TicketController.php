<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Support;

use App\Actions\CreateSupportTicket;
use App\Actions\DestroySupportTicket;
use App\Actions\UpdateSupportTicket;
use App\Enums\SupportCategory;
use App\Enums\SupportTicketStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = $request->user()
            ->supportTickets()
            ->withCount('messages')
            ->with('messages')
            ->latest()
            ->get();

        return view('app.support.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function new(): View
    {
        return view('app.support.tickets.new', [
            'categories' => SupportCategory::cases(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', Rule::enum(SupportCategory::class)],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = new CreateSupportTicket(
            user: $request->user(),
            category: SupportCategory::from($validated['category']),
            subject: $validated['subject'],
            message: $validated['message'],
        )->execute();

        return to_route('support.tickets.show', $ticket)
            ->with('status', trans('Message sent'))
            ->with('status_description', trans('I\'ll reply to you as soon as I can.'));
    }

    public function show(Request $request, int $supportTicket): View
    {
        $ticket = $request->user()
            ->supportTickets()
            ->with('messages.user')
            ->findOrFail($supportTicket);

        return view('app.support.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function update(Request $request, int $supportTicket): RedirectResponse
    {
        $ticket = $request->user()
            ->supportTickets()
            ->findOrFail($supportTicket);

        new UpdateSupportTicket(
            user: $request->user(),
            ticket: $ticket,
            status: SupportTicketStatus::Closed,
        )->execute();

        return to_route('support.tickets.show', $ticket)
            ->with('status', trans('Conversation closed'));
    }

    public function destroy(Request $request, int $supportTicket): RedirectResponse
    {
        $ticket = $request->user()
            ->supportTickets()
            ->findOrFail($supportTicket);

        new DestroySupportTicket(
            user: $request->user(),
            ticket: $ticket,
        )->execute();

        return to_route('support.tickets.index')
            ->with('status', trans('Conversation deleted'));
    }
}
