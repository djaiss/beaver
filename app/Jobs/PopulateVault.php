<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Vault;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
        $this->addDefaultRelationshipTypes();
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

    private function addDefaultRelationshipTypes(): void
    {
        $categories = $this->vault
            ->relationshipTypeCategories()
            ->get()
            ->keyBy('key');

        $typesData = [
            'family' => [
                [
                    'key' => 'parent_child',
                    'name_translation_key' => 'app/shared.relationship_types.parent_child',
                    'forward_name_translation_key' => 'app/shared.relationship_types.parent',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.child',
                    'is_directed' => true,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'sibling',
                    'name_translation_key' => 'app/shared.relationship_types.sibling',
                    'forward_name_translation_key' => 'app/shared.relationship_types.sibling',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.sibling',
                    'is_directed' => false,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'grandparent_grandchild',
                    'name_translation_key' => 'app/shared.relationship_types.grandparent_grandchild',
                    'forward_name_translation_key' => 'app/shared.relationship_types.grandparent',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.grandchild',
                    'is_directed' => true,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'uncle_nephew',
                    'name_translation_key' => 'app/shared.relationship_types.uncle_nephew',
                    'forward_name_translation_key' => 'app/shared.relationship_types.uncle',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.nephew',
                    'is_directed' => true,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'cousin',
                    'name_translation_key' => 'app/shared.relationship_types.cousin',
                    'forward_name_translation_key' => 'app/shared.relationship_types.cousin',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.cousin',
                    'is_directed' => false,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'in_law',
                    'name_translation_key' => 'app/shared.relationship_types.in_law',
                    'forward_name_translation_key' => 'app/shared.relationship_types.in_law',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.in_law',
                    'is_directed' => false,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'guardian_ward',
                    'name_translation_key' => 'app/shared.relationship_types.guardian_ward',
                    'forward_name_translation_key' => 'app/shared.relationship_types.guardian',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.ward',
                    'is_directed' => true,
                ],
            ],

            'romantic' => [
                [
                    'key' => 'partner',
                    'name_translation_key' => 'app/shared.relationship_types.partner',
                    'forward_name_translation_key' => 'app/shared.relationship_types.partner',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.partner',
                    'is_directed' => false,
                ],
                [
                    'key' => 'spouse',
                    'name_translation_key' => 'app/shared.relationship_types.spouse',
                    'forward_name_translation_key' => 'app/shared.relationship_types.spouse',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.spouse',
                    'is_directed' => false,
                ],
                [
                    'key' => 'ex_partner',
                    'name_translation_key' => 'app/shared.relationship_types.ex_partner',
                    'forward_name_translation_key' => 'app/shared.relationship_types.ex_partner',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.ex_partner',
                    'is_directed' => false,
                ],
                [
                    'key' => 'fiance',
                    'name_translation_key' => 'app/shared.relationship_types.fiance',
                    'forward_name_translation_key' => 'app/shared.relationship_types.fiance',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.fiance',
                    'is_directed' => false,
                ],
            ],

            'friendship' => [
                [
                    'key' => 'friend',
                    'name_translation_key' => 'app/shared.relationship_types.friend',
                    'forward_name_translation_key' => 'app/shared.relationship_types.friend',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'close_friend',
                    'name_translation_key' => 'app/shared.relationship_types.close_friend',
                    'forward_name_translation_key' => 'app/shared.relationship_types.close_friend',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.close_friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'acquaintance',
                    'name_translation_key' => 'app/shared.relationship_types.acquaintance',
                    'forward_name_translation_key' => 'app/shared.relationship_types.acquaintance',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.acquaintance',
                    'is_directed' => false,
                ],
            ],

            'professional' => [
                [
                    'key' => 'colleague',
                    'name_translation_key' => 'app/shared.relationship_types.colleague',
                    'forward_name_translation_key' => 'app/shared.relationship_types.colleague',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.colleague',
                    'is_directed' => false,
                ],
                [
                    'key' => 'manager_report',
                    'name_translation_key' => 'app/shared.relationship_types.manager_report',
                    'forward_name_translation_key' => 'app/shared.relationship_types.manager',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.direct_report',
                    'is_directed' => true,
                ],
                [
                    'key' => 'mentor_mentee',
                    'name_translation_key' => 'app/shared.relationship_types.mentor_mentee',
                    'forward_name_translation_key' => 'app/shared.relationship_types.mentor',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.mentee',
                    'is_directed' => true,
                ],
                [
                    'key' => 'client_provider',
                    'name_translation_key' => 'app/shared.relationship_types.client_provider',
                    'forward_name_translation_key' => 'app/shared.relationship_types.client',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.provider',
                    'is_directed' => true,
                ],
                [
                    'key' => 'business_partner',
                    'name_translation_key' => 'app/shared.relationship_types.business_partner',
                    'forward_name_translation_key' => 'app/shared.relationship_types.business_partner',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.business_partner',
                    'is_directed' => false,
                ],
            ],

            'community' => [
                [
                    'key' => 'neighbor',
                    'name_translation_key' => 'app/shared.relationship_types.neighbor',
                    'forward_name_translation_key' => 'app/shared.relationship_types.neighbor',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.neighbor',
                    'is_directed' => false,
                ],
                [
                    'key' => 'club_member',
                    'name_translation_key' => 'app/shared.relationship_types.club_member',
                    'forward_name_translation_key' => 'app/shared.relationship_types.club_member',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.club_member',
                    'is_directed' => false,
                ],
                [
                    'key' => 'teammate',
                    'name_translation_key' => 'app/shared.relationship_types.teammate',
                    'forward_name_translation_key' => 'app/shared.relationship_types.teammate',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.teammate',
                    'is_directed' => false,
                ],
            ],

            'education' => [
                [
                    'key' => 'teacher_student',
                    'name_translation_key' => 'app/shared.relationship_types.teacher_student',
                    'forward_name_translation_key' => 'app/shared.relationship_types.teacher',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.student',
                    'is_directed' => true,
                ],
                [
                    'key' => 'classmate',
                    'name_translation_key' => 'app/shared.relationship_types.classmate',
                    'forward_name_translation_key' => 'app/shared.relationship_types.classmate',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.classmate',
                    'is_directed' => false,
                ],
                [
                    'key' => 'tutor_student',
                    'name_translation_key' => 'app/shared.relationship_types.tutor_student',
                    'forward_name_translation_key' => 'app/shared.relationship_types.tutor',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.student',
                    'is_directed' => true,
                ],
            ],

            'care' => [
                [
                    'key' => 'doctor_patient',
                    'name_translation_key' => 'app/shared.relationship_types.doctor_patient',
                    'forward_name_translation_key' => 'app/shared.relationship_types.doctor',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.patient',
                    'is_directed' => true,
                ],
                [
                    'key' => 'therapist_client',
                    'name_translation_key' => 'app/shared.relationship_types.therapist_client',
                    'forward_name_translation_key' => 'app/shared.relationship_types.therapist',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'caregiver_dependent',
                    'name_translation_key' => 'app/shared.relationship_types.caregiver_dependent',
                    'forward_name_translation_key' => 'app/shared.relationship_types.caregiver',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.dependent',
                    'is_directed' => true,
                ],
            ],

            'service' => [
                [
                    'key' => 'contractor_client',
                    'name_translation_key' => 'app/shared.relationship_types.contractor_client',
                    'forward_name_translation_key' => 'app/shared.relationship_types.contractor',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'lawyer_client',
                    'name_translation_key' => 'app/shared.relationship_types.lawyer_client',
                    'forward_name_translation_key' => 'app/shared.relationship_types.lawyer',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'accountant_client',
                    'name_translation_key' => 'app/shared.relationship_types.accountant_client',
                    'forward_name_translation_key' => 'app/shared.relationship_types.accountant',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'coach_client',
                    'name_translation_key' => 'app/shared.relationship_types.coach_client',
                    'forward_name_translation_key' => 'app/shared.relationship_types.coach',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.client',
                    'is_directed' => true,
                ],
            ],

            'household' => [
                [
                    'key' => 'roommate',
                    'name_translation_key' => 'app/shared.relationship_types.roommate',
                    'forward_name_translation_key' => 'app/shared.relationship_types.roommate',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.roommate',
                    'is_directed' => false,
                ],
                [
                    'key' => 'landlord_tenant',
                    'name_translation_key' => 'app/shared.relationship_types.landlord_tenant',
                    'forward_name_translation_key' => 'app/shared.relationship_types.landlord',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.tenant',
                    'is_directed' => true,
                ],
                [
                    'key' => 'household_member',
                    'name_translation_key' => 'app/shared.relationship_types.household_member',
                    'forward_name_translation_key' => 'app/shared.relationship_types.household_member',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.household_member',
                    'is_directed' => false,
                ],
            ],

            'online' => [
                [
                    'key' => 'online_friend',
                    'name_translation_key' => 'app/shared.relationship_types.online_friend',
                    'forward_name_translation_key' => 'app/shared.relationship_types.online_friend',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.online_friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'open_source_collaborator',
                    'name_translation_key' => 'app/shared.relationship_types.open_source_collaborator',
                    'forward_name_translation_key' => 'app/shared.relationship_types.open_source_collaborator',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.open_source_collaborator',
                    'is_directed' => false,
                ],
                [
                    'key' => 'gaming_friend',
                    'name_translation_key' => 'app/shared.relationship_types.gaming_friend',
                    'forward_name_translation_key' => 'app/shared.relationship_types.gaming_friend',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.gaming_friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'follower_following',
                    'name_translation_key' => 'app/shared.relationship_types.follower_following',
                    'forward_name_translation_key' => 'app/shared.relationship_types.following',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.follower',
                    'is_directed' => true,
                ],
            ],

            'organization' => [
                [
                    'key' => 'member',
                    'name_translation_key' => 'app/shared.relationship_types.member',
                    'forward_name_translation_key' => 'app/shared.relationship_types.member',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.member',
                    'is_directed' => false,
                ],
                [
                    'key' => 'leader_member',
                    'name_translation_key' => 'app/shared.relationship_types.leader_member',
                    'forward_name_translation_key' => 'app/shared.relationship_types.leader',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.member',
                    'is_directed' => true,
                ],
                [
                    'key' => 'board_member',
                    'name_translation_key' => 'app/shared.relationship_types.board_member',
                    'forward_name_translation_key' => 'app/shared.relationship_types.board_member',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.board_member',
                    'is_directed' => false,
                ],
            ],

            'other' => [
                [
                    'key' => 'knows',
                    'name_translation_key' => 'app/shared.relationship_types.knows',
                    'forward_name_translation_key' => 'app/shared.relationship_types.knows',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.known_by',
                    'is_directed' => true,
                ],
                [
                    'key' => 'connected_to',
                    'name_translation_key' => 'app/shared.relationship_types.connected_to',
                    'forward_name_translation_key' => 'app/shared.relationship_types.connected_to',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.connected_to',
                    'is_directed' => false,
                ],
                [
                    'key' => 'related_to',
                    'name_translation_key' => 'app/shared.relationship_types.related_to',
                    'forward_name_translation_key' => 'app/shared.relationship_types.related_to',
                    'reverse_name_translation_key' => 'app/shared.relationship_types.related_to',
                    'is_directed' => false,
                ],
            ],
        ];

        $now = now();

        DB::transaction(function () use ($typesData, $categories, $now): void {
            foreach ($typesData as $categoryKey => $types) {
                $category = $categories->get($categoryKey);

                if (! $category) {
                    continue;
                }

                $category
                    ->relationshipTypes()
                    ->insert(
                        collect($types)
                            ->values()
                            ->map(fn ($type, $index): array => [
                                'vault_id' => $this->vault->id,
                                'relationship_type_category_id' => $category->id,
                                'key' => Crypt::encryptString($type['key']),
                                'name' => null,
                                'name_translation_key' => Crypt::encryptString($type['name_translation_key']),
                                'forward_name' => null,
                                'forward_name_translation_key' => Crypt::encryptString(
                                    $type['forward_name_translation_key'],
                                ),
                                'reverse_name' => null,
                                'reverse_name_translation_key' => Crypt::encryptString(
                                    $type['reverse_name_translation_key'],
                                ),
                                'is_directed' => $type['is_directed'],
                                'can_be_deleted' => $type['can_be_deleted'] ?? true,
                                'position' => $index + 1,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ])
                            ->all(),
                    );
            }
        });
    }
}
