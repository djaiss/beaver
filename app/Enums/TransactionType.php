<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * What kind of exchange a transaction records.
 *
 * The types split in two. Some are a real change of ownership and are part of
 * the object's story, so they may carry a provenance event. The rest are the
 * money around a change of ownership rather than the change itself, and a fee or
 * a shipping charge has no place in a narrative of who owned the thing.
 */
enum TransactionType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case Trade = 'trade';
    case GiftReceived = 'gift_received';
    case GiftGiven = 'gift_given';
    case Inheritance = 'inheritance';
    case Refund = 'refund';
    case Fee = 'fee';
    case Tax = 'tax';
    case Shipping = 'shipping';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Purchase => __('Purchase'),
            self::Sale => __('Sale'),
            self::Trade => __('Trade'),
            self::GiftReceived => __('Gift received'),
            self::GiftGiven => __('Gift given'),
            self::Inheritance => __('Inheritance'),
            self::Refund => __('Refund'),
            self::Fee => __('Fee'),
            self::Tax => __('Tax'),
            self::Shipping => __('Shipping'),
            self::Other => __('Other'),
        };
    }

    /**
     * Whether this type is a real change of ownership, and so may carry a
     * provenance event.
     */
    public function qualifiesForProvenance(): bool
    {
        return in_array($this, [
            self::Purchase,
            self::Sale,
            self::Trade,
            self::GiftReceived,
            self::GiftGiven,
            self::Inheritance,
        ], true);
    }

    /**
     * Whether this type brings the copy into the collection.
     *
     * The acquisition date and the purchase price of a copy are read from the
     * earliest transaction of one of these types.
     */
    public function acquires(): bool
    {
        return in_array($this, [
            self::Purchase,
            self::Trade,
            self::GiftReceived,
            self::Inheritance,
        ], true);
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
