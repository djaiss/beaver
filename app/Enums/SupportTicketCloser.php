<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Who closed a support conversation.
 *
 * The user can close their own conversation, and one day the support team will
 * be able to close it from the other side. Remembering which one it was lets the
 * closed notice say "Closed by you" or "Closed by the support team".
 */
enum SupportTicketCloser: string
{
    case User = 'user';
    case Team = 'team';

    public function label(): string
    {
        return match ($this) {
            self::User => __('you'),
            self::Team => __('the support team'),
        };
    }
}
