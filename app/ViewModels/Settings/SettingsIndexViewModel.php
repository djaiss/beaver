<?php

declare(strict_types=1);

namespace App\ViewModels\Settings;

use App\Models\EmailSent;
use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Collection;

class SettingsIndexViewModel
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function logs(): Collection
    {
        return Log::query()
            ->where('user_id', $this->user->id)
            ->with('user')
            ->with('vault')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Log $log) => (object) [
                'username' => $log->getUserName(),
                'vault_name' => $log->vault?->name,
                'vault_link' => $log->vault ? route('vault.show', $log->vault_id) : null,
                'action' => $log->action,
                'description' => $log->getTranslatedDescription(),
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
            ]);
    }

    public function hasMoreLogs(): bool
    {
        return Log::query()->where('user_id', $this->user->id)->count() > 5;
    }

    public function emails(): Collection
    {
        return EmailSent::query()
            ->where('user_id', $this->user->id)
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
    }

    public function hasMoreEmails(): bool
    {
        return EmailSent::query()->where('user_id', $this->user->id)->count() > 6;
    }

    public function user(): object
    {
        return (object) $this->user
            ->only([
                'first_name',
                'last_name',
                'nickname',
                'email',
                'locale',
                'time_format_24h',
            ]);
    }

    public function url(): object
    {
        return (object) [
            'dashboard' => route('vault.index'),
            'profileUpdate' => route('settings.profile.update'),
            'logs' => route('settings.logs.index'),
        ];
    }
}
