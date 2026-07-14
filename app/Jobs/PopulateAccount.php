<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\FieldTypeEnum;
use App\Models\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class PopulateAccount implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Account $account,
    ) {}

    /**
     * Populate the account with the default collection types and their fields.
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            foreach ($this->defaultTypes() as $type) {
                $this->createType($type);
            }
        });
    }

    /**
     * @param  array{name: string, color: string, fields: list<array{name: string, field_type: FieldTypeEnum, options?: list<string>}>}  $type
     */
    private function createType(array $type): void
    {
        $collectionType = $this->account->collectionTypes()->create([
            'name' => $type['name'],
            'color' => $type['color'],
        ]);

        $collectionType->customFields()->createMany(
            collect($type['fields'])
                ->map(fn (array $field, int $index): array => [
                    'name' => $field['name'],
                    'field_type' => $field['field_type'],
                    'options' => $field['options'] ?? null,
                    'position' => $index + 1,
                ])
                ->all()
        );
    }

    /**
     * @return list<array{name: string, color: string, fields: list<array{name: string, field_type: FieldTypeEnum, options?: list<string>}>}>
     */
    private function defaultTypes(): array
    {
        return [
            [
                'name' => 'Comics',
                'color' => '#EF4444',
                'fields' => [
                    ['name' => 'Issue #', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Publisher', 'field_type' => FieldTypeEnum::Select, 'options' => ['Marvel', 'DC', 'Image', 'Dark Horse', 'Independent']],
                    ['name' => 'Writer', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Artist', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Cover Date', 'field_type' => FieldTypeEnum::Date],
                    ['name' => 'Variant', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Signed', 'field_type' => FieldTypeEnum::Boolean],
                ],
            ],
            [
                'name' => 'Trading Cards',
                'color' => '#F59E0B',
                'fields' => [
                    ['name' => 'Card Number', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Set/Series', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Player/Character', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Grade', 'field_type' => FieldTypeEnum::Select, 'options' => ['PSA 10', 'PSA 9', 'Raw']],
                    ['name' => 'Rookie Card', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Autographed', 'field_type' => FieldTypeEnum::Boolean],
                ],
            ],
            [
                'name' => 'Vinyl Records',
                'color' => '#8B5CF6',
                'fields' => [
                    ['name' => 'Artist', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Album', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Pressing/Edition', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Release Year', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Speed', 'field_type' => FieldTypeEnum::Select, 'options' => ['33', '45', '78']],
                    ['name' => 'Color Vinyl', 'field_type' => FieldTypeEnum::Text],
                ],
            ],
            [
                'name' => 'CD',
                'color' => '#6366F1',
                'fields' => [
                    ['name' => 'Artist', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Album', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Label', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Release Year', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Genre', 'field_type' => FieldTypeEnum::Select],
                    ['name' => 'Bonus Disc', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Signed', 'field_type' => FieldTypeEnum::Boolean],
                ],
            ],
            [
                'name' => 'DVD',
                'color' => '#EC4899',
                'fields' => [
                    ['name' => 'Title', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Director', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Studio', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Release Year', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Region Code', 'field_type' => FieldTypeEnum::Select, 'options' => ['1', '2', '3', '4', '5', '6', 'Free']],
                    ['name' => 'Special Edition', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Box Set', 'field_type' => FieldTypeEnum::Boolean],
                ],
            ],
            [
                'name' => 'Coins',
                'color' => '#EAB308',
                'fields' => [
                    ['name' => 'Denomination', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Year Minted', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Mint Mark', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Country', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Grade', 'field_type' => FieldTypeEnum::Select, 'options' => ['MS-70', 'MS-65', 'MS-60', 'AU-58', 'AU-50', 'XF-45', 'XF-40', 'VF-30', 'VF-20', 'F-15', 'F-12', 'VG-10', 'VG-8', 'G-6', 'G-4', 'AG-3', 'FR-2', 'PR-1']],
                    ['name' => 'Metal', 'field_type' => FieldTypeEnum::Select, 'options' => ['Silver', 'Gold', 'Copper']],
                ],
            ],
            [
                'name' => 'Stamps',
                'color' => '#10B981',
                'fields' => [
                    ['name' => 'Country', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Year Issued', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Denomination', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Perforated', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Cancelled/Mint', 'field_type' => FieldTypeEnum::Select, 'options' => ['Cancelled', 'Mint']],
                ],
            ],
            [
                'name' => 'Books',
                'color' => '#3B82F6',
                'fields' => [
                    ['name' => 'Author', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Edition', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Publisher', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'ISBN', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'First Edition', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Signed', 'field_type' => FieldTypeEnum::Boolean],
                ],
            ],
            [
                'name' => 'Action Figures / Toys',
                'color' => '#F97316',
                'fields' => [
                    ['name' => 'Manufacturer', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Line/Series', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Year', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'In Box / MIB', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Scale', 'field_type' => FieldTypeEnum::Text],
                ],
            ],
            [
                'name' => 'Video Games',
                'color' => '#14B8A6',
                'fields' => [
                    ['name' => 'Platform', 'field_type' => FieldTypeEnum::Select, 'options' => ['NES', 'SNES', 'PS1']],
                    ['name' => 'Publisher', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Release Year', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Complete In Box', 'field_type' => FieldTypeEnum::Boolean],
                    ['name' => 'Region', 'field_type' => FieldTypeEnum::Select, 'options' => ['NTSC', 'PAL']],
                ],
            ],
            [
                'name' => 'Watches',
                'color' => '#64748B',
                'fields' => [
                    ['name' => 'Brand', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Model', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Movement', 'field_type' => FieldTypeEnum::Select, 'options' => ['Automatic', 'Quartz', 'Manual']],
                    ['name' => 'Serial Number', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Box & Papers', 'field_type' => FieldTypeEnum::Boolean],
                ],
            ],
            [
                'name' => 'Wine',
                'color' => '#991B1B',
                'fields' => [
                    ['name' => 'Producer/Winery', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Vintage', 'field_type' => FieldTypeEnum::Number],
                    ['name' => 'Region', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Varietal', 'field_type' => FieldTypeEnum::Text],
                    ['name' => 'Bottle Size', 'field_type' => FieldTypeEnum::Select, 'options' => ['Split/187ml', 'Half/375ml', 'Standard/750ml', 'Magnum/1.5L', 'Double Magnum/3L', 'Jeroboam/4.5L', 'Imperial/6L']],
                ],
            ],
        ];
    }
}
