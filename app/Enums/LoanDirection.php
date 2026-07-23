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
     * The kebab-case slug that carries the direction in a URL path, keeping the
     * loans section navigable without query strings.
     */
    public function slug(): string
    {
        return match ($this) {
            self::Outgoing => 'lent-out',
            self::Incoming => 'borrowed-in',
        };
    }

    /**
     * Resolve a direction from its URL slug.
     */
    public static function fromSlug(string $slug): self
    {
        return match ($slug) {
            'lent-out' => self::Outgoing,
            'borrowed-in' => self::Incoming,
            default => throw new \ValueError("Unknown loan direction slug [{$slug}]."),
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
