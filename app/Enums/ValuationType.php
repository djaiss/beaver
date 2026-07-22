<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a valuation came from.
 *
 * A purchase price is never one of these: what was actually paid belongs to a
 * transaction, and a valuation is only ever an estimate of worth.
 */
enum ValuationType: string
{
    case UserEstimate = 'user_estimate';
    case ProfessionalAppraisal = 'professional_appraisal';
    case MarketEstimate = 'market_estimate';
    case InsuranceValue = 'insurance_value';
    case AuctionEstimate = 'auction_estimate';
    case AutomatedEstimate = 'automated_estimate';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::UserEstimate => __('Own estimate'),
            self::ProfessionalAppraisal => __('Professional appraisal'),
            self::MarketEstimate => __('Market estimate'),
            self::InsuranceValue => __('Insurance value'),
            self::AuctionEstimate => __('Auction estimate'),
            self::AutomatedEstimate => __('Automated estimate'),
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
