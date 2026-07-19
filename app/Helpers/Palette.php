<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * The colours the app paints a category with, in the sidebar and on the category
 * page. A category has no colour of its own, so one is picked from its id: the
 * same category then keeps the same colour on every screen, whatever order the
 * screen happens to list it in.
 */
class Palette
{
    /** @var list<string> */
    private const array COLOURS = ['#3b82f6', '#8b5cf6', '#34d399', '#fb923c', '#ec4899'];

    public static function forId(int $id): string
    {
        return self::COLOURS[$id % count(self::COLOURS)];
    }
}
