<?php

declare(strict_types=1);

namespace App\Helpers;

class FileSize
{
    private const int STEP = 1024;

    /**
     * A size in bytes written the way a person reads it, e.g. "3.1 MB".
     *
     * Bytes are shown whole, since half a byte means nothing, and every larger
     * unit keeps one decimal.
     */
    public static function format(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $value = max($bytes, 0);
        $unit = 0;

        while ($value >= self::STEP && $unit < count($units) - 1) {
            $value /= self::STEP;
            $unit++;
        }

        if ($unit === 0) {
            return $value.' '.$units[$unit];
        }

        return round($value, 1).' '.$units[$unit];
    }
}
