<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\UpdateUserInformation;
use App\Http\Controllers\Controller;
use App\Models\EmailSent;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $logs = Log::query()
            ->where('user_id', $request->user()->id)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Log $log) => (object) [
                'username' => $log->getUserName(),
                'action' => $log->action,
                'description' => $log->getTranslatedDescription(),
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
            ]);

        $hasMoreLogs = Log::query()->where('user_id', $request->user()->id)->count() > 5;

        $emails = EmailSent::query()
            ->where('user_id', $request->user()->id)
            ->latest('sent_at')
            ->limit(6)
            ->get()
            ->map(fn (EmailSent $email) => (object) [
                'email_address' => $email->email_address,
                'subject' => $email->subject,
                'body' => $email->body,
                'sent_at' => $email->sent_at,
                'delivered_at' => $email->delivered_at,
                'bounced_at' => $email->bounced_at,
            ]);

        $hasMoreEmails = EmailSent::query()->where('user_id', $request->user()->id)->count() > 6;

        $user = (object) $request
            ->user()
            ->only([
                'first_name',
                'last_name',
                'nickname',
                'email',
                'locale',
                'time_format_24h',
            ]);

        return view('app.settings.index', [
            'user' => $user,
            'logs' => $logs,
            'hasMoreLogs' => $hasMoreLogs,
            'emails' => $emails,
            'hasMoreEmails' => $hasMoreEmails,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(Auth::user()->id),
                'disposable_email',
            ],
            'locale' => ['required', 'string', 'max:5', Rule::in(config('app.supported_locales'))],
            'time_format_24h' => ['required', Rule::in(['true', 'false'])],
        ]);

        new UpdateUserInformation(
            user: $request->user(),
            email: mb_strtolower((string) $validated['email']),
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            nickname: $validated['nickname'],
            locale: $validated['locale'],
            timeFormat24h: $validated['time_format_24h'] === 'true',
        )->execute();

        return to_route('profile.index')
            ->with('status', __('Changes saved'));
    }
}
