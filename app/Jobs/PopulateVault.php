<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Vault;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PopulateVault implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Vault $vault,
    ) {}

    /**
     * Populate the vault with initial data.
     */
    public function handle(): void
    {
        $this->addDefaultGenders();
        $this->addDefaultRelationshipTypeCategories();
    }

    private function addDefaultGenders(): void
    {
        $gendersData = [
            [
                'name_translation_key' => 'app/shared.genders.man',
                'position' => 1,
            ],
            [
                'name_translation_key' => 'app/shared.genders.woman',
                'position' => 2,
            ],
            [
                'name_translation_key' => 'app/shared.genders.other',
                'position' => 3,
            ],
        ];

        $this->vault->genders()->createMany($gendersData);
    }

    private function addDefaultRelationshipTypeCategories(): void
    {
        $categoriesData = [
            [
                'key' => 'family',
                'name_translation_key' => 'app/shared.relationship_type_categories.family',
                'position' => 1,
                'can_be_deleted' => false,
            ],
            [
                'key' => 'romantic',
                'name_translation_key' => 'app/shared.relationship_type_categories.romantic',
                'position' => 2,
                'can_be_deleted' => false,
            ],
            [
                'key' => 'friendship',
                'name_translation_key' => 'app/shared.relationship_type_categories.friendship',
                'position' => 3,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'professional',
                'name_translation_key' => 'app/shared.relationship_type_categories.professional',
                'position' => 4,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'community',
                'name_translation_key' => 'app/shared.relationship_type_categories.community',
                'position' => 5,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'education',
                'name_translation_key' => 'app/shared.relationship_type_categories.education',
                'position' => 6,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'care',
                'name_translation_key' => 'app/shared.relationship_type_categories.care',
                'position' => 7,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'service',
                'name_translation_key' => 'app/shared.relationship_type_categories.service',
                'position' => 8,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'household',
                'name_translation_key' => 'app/shared.relationship_type_categories.household',
                'position' => 9,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'online',
                'name_translation_key' => 'app/shared.relationship_type_categories.online',
                'position' => 10,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'organization',
                'name_translation_key' => 'app/shared.relationship_type_categories.organization',
                'position' => 11,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'other',
                'name_translation_key' => 'app/shared.relationship_type_categories.other',
                'position' => 12,
                'can_be_deleted' => true,
            ],
        ];

        $this->vault->relationshipTypeCategories()->createMany($categoriesData);
    }
}
