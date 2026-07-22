<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * What kind of moment a provenance event records.
 *
 * These are the moments that belong to the object's story. Routine work does
 * not: ordinary cleaning, everyday storage changes and informal personal loans
 * stay in their own models. A significant restoration, an institutional loan, an
 * exhibition, an authentication and a real change of ownership do belong here.
 */
enum ProvenanceEventType: string
{
    case Acquisition = 'acquisition';
    case Sale = 'sale';
    case Gift = 'gift';
    case Inheritance = 'inheritance';
    case OwnershipTransfer = 'ownership_transfer';
    case CustodyTransfer = 'custody_transfer';
    case Loan = 'loan';
    case Return = 'return';
    case Exhibition = 'exhibition';
    case Authentication = 'authentication';
    case Appraisal = 'appraisal';
    case SignificantRestoration = 'significant_restoration';
    case Origin = 'origin';
    case Discovery = 'discovery';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Acquisition => __('Acquisition'),
            self::Sale => __('Sale'),
            self::Gift => __('Gift'),
            self::Inheritance => __('Inheritance'),
            self::OwnershipTransfer => __('Ownership transfer'),
            self::CustodyTransfer => __('Custody transfer'),
            self::Loan => __('Loan'),
            self::Return => __('Return'),
            self::Exhibition => __('Exhibition'),
            self::Authentication => __('Authentication'),
            self::Appraisal => __('Appraisal'),
            self::SignificantRestoration => __('Significant restoration'),
            self::Origin => __('Origin'),
            self::Discovery => __('Discovery'),
            self::Other => __('Other'),
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
