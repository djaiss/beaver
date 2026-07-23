<?php

declare(strict_types=1);

namespace App\Helpers;

use Stevebauman\Purify\Facades\Purify;

/**
 * Provides utility methods for cleaning user input.
 *
 * This class removes potentially dangerous HTML/PHP tags from user-submitted
 * text to prevent XSS (cross-site scripting) attacks.
 */
class TextSanitizer
{
    /**
     * Remove all HTML and return plain text only.
     * Uses HTMLPurifier with no allowed tags instead of strip_tags.
     * The purifier HTML-encodes its output, so decode entities to store
     * real plain text; Blade escapes it again at render time.
     */
    public static function plainText(string $value): string
    {
        $stripped = Purify::config(['HTML.Allowed' => ''])->clean($value);

        $decoded = html_entity_decode($stripped, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return mb_trim($decoded);
    }

    /**
     * Plain text sanitization that returns null for empty results.
     */
    public static function nullablePlainText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitized = self::plainText($value);

        return $sanitized === '' ? null : $sanitized;
    }

    /**
     * Sanitize rich HTML, preserving safe formatting tags.
     */
    public static function html(string $value): string
    {
        return Purify::clean($value);
    }

    /**
     * Rich HTML sanitization that returns null for empty results.
     */
    public static function nullableHtml(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $sanitized = mb_trim(strip_tags(self::html($value)));

        return $sanitized === '' ? null : $sanitized;
    }
}
