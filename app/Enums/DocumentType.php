<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * What a document is, from the point of view of the record it backs.
 *
 * A document proves or supports something about a copy or one of the records
 * hanging off it: a receipt for a transaction, an appraisal report for a
 * valuation, a policy schedule for an insurance record, a certificate backing an
 * authentication, a restoration report for maintenance work. The type is what the
 * document is, not what it is attached to, so the same list serves every parent.
 */
enum DocumentType: string
{
    case Receipt = 'receipt';
    case Invoice = 'invoice';
    case Certificate = 'certificate';
    case Appraisal = 'appraisal';
    case Insurance = 'insurance';
    case Photograph = 'photograph';
    case ConditionReport = 'condition_report';
    case RestorationReport = 'restoration_report';
    case Catalogue = 'catalogue';
    case Correspondence = 'correspondence';
    case OwnershipRecord = 'ownership_record';
    case AuthenticityRecord = 'authenticity_record';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Receipt => __('Receipt'),
            self::Invoice => __('Invoice'),
            self::Certificate => __('Certificate'),
            self::Appraisal => __('Appraisal'),
            self::Insurance => __('Insurance'),
            self::Photograph => __('Photograph'),
            self::ConditionReport => __('Condition report'),
            self::RestorationReport => __('Restoration report'),
            self::Catalogue => __('Catalogue'),
            self::Correspondence => __('Correspondence'),
            self::OwnershipRecord => __('Ownership record'),
            self::AuthenticityRecord => __('Authenticity record'),
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
