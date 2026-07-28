<?php

declare(strict_types=1);

namespace App\Enums;

enum PurchaseConsentChoice: string
{
    case NonRefundable = 'non_refundable';
    case NoChargeback = 'no_chargeback';
    case SelfHostingIsFree = 'self_hosting_is_free';
    case UnlockCovers = 'unlock_covers';

    /**
     * The sentence somebody ticks on the confirmation screen. It is written in
     * the first person because they are the one agreeing to it.
     */
    public function label(): string
    {
        return match ($this) {
            self::NonRefundable => __('I understand this is a single :price payment, and that it is not refundable.', ['price' => '$'.config('pricing.price')]),
            self::NoChargeback => __('I will not open a chargeback with my bank. I will email support first if something goes wrong.'),
            self::SelfHostingIsFree => __('I know self-hosting :name is free, and I am choosing the hosted account on purpose.', ['name' => config('app.name')]),
            self::UnlockCovers => __('I have looked at what the unlock includes and it covers what I need.'),
        };
    }

    /**
     * The same point in the third person, for the instance administration panel.
     * That panel is never translated, so this stays a plain string.
     */
    public function summary(): string
    {
        return match ($this) {
            self::NonRefundable => 'Accepted that the payment is a single charge and not refundable',
            self::NoChargeback => 'Agreed to email support rather than open a chargeback',
            self::SelfHostingIsFree => 'Acknowledged that self-hosting is free and chose hosting anyway',
            self::UnlockCovers => 'Confirmed the unlock covers what they need',
        };
    }
}
