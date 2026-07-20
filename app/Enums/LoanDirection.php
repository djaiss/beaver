<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Which way custody moves in a loan.
 *
 * A loan never changes ownership, only who physically holds the object. It is
 * outgoing when a piece is lent out to a friend, a gallery or a museum, and
 * incoming when a piece is borrowed in from someone else.
 */
enum LoanDirection: string
{
    case Outgoing = 'outgoing';
    case Incoming = 'incoming';

    public function label(): string
    {
        return match ($this) {
            self::Outgoing => __('Lent out'),
            self::Incoming => __('Borrowed in'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
