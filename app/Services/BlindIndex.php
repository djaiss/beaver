<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Turns text that is encrypted at rest into hashes a database can search.
 *
 * An encrypted column cannot be matched with LIKE, so the words worth searching
 * are hashed with a keyed hash and stored next to the row instead. Every prefix
 * of a word is hashed too, so typing "spid" still finds "spiderman", which is
 * what a search box is expected to do.
 *
 * Two things follow from only indexing prefixes. A word is matched from its
 * start rather than anywhere inside it, and a query longer than the longest
 * prefix kept is matched on that prefix alone, so it can return a little more
 * than what was typed.
 */
class BlindIndex
{
    /**
     * Words shorter than this are still indexed whole, so a two letter word
     * remains findable.
     */
    private const int MIN_LENGTH = 2;

    private const int MAX_LENGTH = 12;

    /**
     * A ceiling on how many rows one record can add to the index, so a
     * pathological file name cannot flood the table.
     */
    private const int MAX_HASHES = 150;

    /**
     * Every hash to store for the given values.
     *
     * @return list<string>
     */
    public static function hashesFor(string ...$values): array
    {
        $hashes = [];

        foreach (self::words(implode(' ', $values)) as $word) {
            $length = mb_strlen($word);
            $longest = min($length, self::MAX_LENGTH);

            for ($size = min(self::MIN_LENGTH, $length); $size <= $longest; $size++) {
                $hashes[] = self::hash(mb_substr($word, 0, $size));
            }
        }

        return array_slice(array_values(array_unique($hashes)), 0, self::MAX_HASHES);
    }

    /**
     * The hashes a query has to match, one per word typed. A single letter is
     * dropped: it is never indexed on its own, so keeping it would match
     * nothing at all.
     *
     * @return list<string>
     */
    public static function hashesForQuery(string $query): array
    {
        $hashes = [];

        foreach (self::words($query) as $word) {
            if (mb_strlen($word) < self::MIN_LENGTH) {
                continue;
            }

            $hashes[] = self::hash(mb_substr($word, 0, self::MAX_LENGTH));
        }

        return array_values(array_unique($hashes));
    }

    public static function hash(string $term): string
    {
        return hash_hmac('sha256', $term, self::key());
    }

    /**
     * Lowercased words, with anything that is neither a letter nor a digit
     * treated as a separator, so "kob_front_cover.jpg" counts as four words.
     *
     * @return list<string>
     */
    private static function words(string $value): array
    {
        $normalized = (string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($value));

        return array_values(array_unique(array_filter(explode(' ', $normalized))));
    }

    /**
     * Derived from the application key, so the index is worthless to anyone who
     * walks away with the database alone.
     */
    private static function key(): string
    {
        $key = (string) config('app.key');

        if (! str_starts_with($key, 'base64:')) {
            return $key;
        }

        return (string) base64_decode(mb_substr($key, 7), true);
    }
}
