<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a support conversation stands.
 *
 * A conversation opens as Open and stays there until the user closes it. There is
 * no staff side yet, so nothing else moves it: replies from the instance team and
 * an Answered state will come when the inbox is built.
 */
enum SupportTicketStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
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
            self::Closed => 'default',
        };
    }
}
