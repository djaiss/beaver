<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a support conversation stands.
 *
 * A conversation opens as Open, waiting on the team. When the team replies it
 * becomes Answered, waiting on the user; the user replying moves it back to Open.
 * Either side can Close it, and a reply from either side reopens a closed one.
 */
enum SupportTicketStatus: string
{
    case Open = 'open';
    case Answered = 'answered';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
            self::Answered => __('Answered'),
            self::Closed => __('Closed'),
        };
    }

    /**
     * The badge colour, mapped to the palette the badge component understands.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Open => 'success',
            self::Answered => 'violet',
            self::Closed => 'default',
        };
    }
}
