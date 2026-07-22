<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Formats the amounts stored in cents for display.
 */
class Money
{
    /** @var array<string, string> */
    private const array SYMBOLS = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥'];

    /**
     * A currency we have no symbol for reads as "1,200 CHF", and one we cannot
     * name at all reads as the bare amount.
     */
    public static function format(int $cents, ?string $currency): string
    {
        $amount = number_format($cents / 100);

        if ($currency === null) {
            return $amount;
        }

        $symbol = self::SYMBOLS[$currency] ?? null;

        return $symbol === null ? $amount.' '.$currency : $symbol.$amount;
    }
}
