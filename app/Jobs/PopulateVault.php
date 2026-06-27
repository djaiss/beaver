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
                'name_translation_key' => 'Man',
                'position' => 1,
            ],
            [
                'name_translation_key' => 'Woman',
                'position' => 2,
            ],
            [
                'name_translation_key' => 'Other',
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
                'name_translation_key' => 'Family',
                'position' => 1,
                'can_be_deleted' => false,
            ],
            [
                'key' => 'romantic',
                'name_translation_key' => 'Romantic',
                'position' => 2,
                'can_be_deleted' => false,
            ],
            [
                'key' => 'friendship',
                'name_translation_key' => 'Friendship',
                'position' => 3,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'professional',
                'name_translation_key' => 'Professional',
                'position' => 4,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'community',
                'name_translation_key' => 'Community',
                'position' => 5,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'education',
                'name_translation_key' => 'Education',
                'position' => 6,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'care',
                'name_translation_key' => 'Care',
                'position' => 7,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'service',
                'name_translation_key' => 'Service',
                'position' => 8,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'household',
                'name_translation_key' => 'Household',
                'position' => 9,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'online',
                'name_translation_key' => 'Online',
                'position' => 10,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'organization',
                'name_translation_key' => 'Organization',
                'position' => 11,
                'can_be_deleted' => true,
            ],
            [
                'key' => 'other',
                'name_translation_key' => 'Other',
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
                    'name_translation_key' => 'Parent / child',
                    'forward_name_translation_key' => 'Parent',
                    'reverse_name_translation_key' => 'Child',
                    'is_directed' => true,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'sibling',
                    'name_translation_key' => 'Sibling',
                    'forward_name_translation_key' => 'Sibling',
                    'reverse_name_translation_key' => 'Sibling',
                    'is_directed' => false,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'grandparent_grandchild',
                    'name_translation_key' => 'Grandparent / grandchild',
                    'forward_name_translation_key' => 'Grandparent',
                    'reverse_name_translation_key' => 'Grandchild',
                    'is_directed' => true,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'uncle_nephew',
                    'name_translation_key' => 'Uncle or aunt / nephew or niece',
                    'forward_name_translation_key' => 'Uncle or aunt',
                    'reverse_name_translation_key' => 'Nephew or niece',
                    'is_directed' => true,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'cousin',
                    'name_translation_key' => 'Cousin',
                    'forward_name_translation_key' => 'Cousin',
                    'reverse_name_translation_key' => 'Cousin',
                    'is_directed' => false,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'in_law',
                    'name_translation_key' => 'In-law',
                    'forward_name_translation_key' => 'In-law',
                    'reverse_name_translation_key' => 'In-law',
                    'is_directed' => false,
                    'can_be_deleted' => false,
                ],
                [
                    'key' => 'guardian_ward',
                    'name_translation_key' => 'Guardian / ward',
                    'forward_name_translation_key' => 'Guardian',
                    'reverse_name_translation_key' => 'Ward',
                    'is_directed' => true,
                ],
            ],

            'romantic' => [
                [
                    'key' => 'partner',
                    'name_translation_key' => 'Partner',
                    'forward_name_translation_key' => 'Partner',
                    'reverse_name_translation_key' => 'Partner',
                    'is_directed' => false,
                ],
                [
                    'key' => 'spouse',
                    'name_translation_key' => 'Spouse',
                    'forward_name_translation_key' => 'Spouse',
                    'reverse_name_translation_key' => 'Spouse',
                    'is_directed' => false,
                ],
                [
                    'key' => 'ex_partner',
                    'name_translation_key' => 'Ex-partner',
                    'forward_name_translation_key' => 'Ex-partner',
                    'reverse_name_translation_key' => 'Ex-partner',
                    'is_directed' => false,
                ],
                [
                    'key' => 'fiance',
                    'name_translation_key' => 'Fiancé(e)',
                    'forward_name_translation_key' => 'Fiancé(e)',
                    'reverse_name_translation_key' => 'Fiancé(e)',
                    'is_directed' => false,
                ],
            ],

            'friendship' => [
                [
                    'key' => 'friend',
                    'name_translation_key' => 'Friend',
                    'forward_name_translation_key' => 'Friend',
                    'reverse_name_translation_key' => 'Friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'close_friend',
                    'name_translation_key' => 'Close friend',
                    'forward_name_translation_key' => 'Close friend',
                    'reverse_name_translation_key' => 'Close friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'acquaintance',
                    'name_translation_key' => 'Acquaintance',
                    'forward_name_translation_key' => 'Acquaintance',
                    'reverse_name_translation_key' => 'Acquaintance',
                    'is_directed' => false,
                ],
            ],

            'professional' => [
                [
                    'key' => 'colleague',
                    'name_translation_key' => 'Colleague',
                    'forward_name_translation_key' => 'Colleague',
                    'reverse_name_translation_key' => 'Colleague',
                    'is_directed' => false,
                ],
                [
                    'key' => 'manager_report',
                    'name_translation_key' => 'Manager / direct report',
                    'forward_name_translation_key' => 'Manager',
                    'reverse_name_translation_key' => 'Direct report',
                    'is_directed' => true,
                ],
                [
                    'key' => 'mentor_mentee',
                    'name_translation_key' => 'Mentor / mentee',
                    'forward_name_translation_key' => 'Mentor',
                    'reverse_name_translation_key' => 'Mentee',
                    'is_directed' => true,
                ],
                [
                    'key' => 'client_provider',
                    'name_translation_key' => 'Client / provider',
                    'forward_name_translation_key' => 'Client',
                    'reverse_name_translation_key' => 'Provider',
                    'is_directed' => true,
                ],
                [
                    'key' => 'business_partner',
                    'name_translation_key' => 'Business partner',
                    'forward_name_translation_key' => 'Business partner',
                    'reverse_name_translation_key' => 'Business partner',
                    'is_directed' => false,
                ],
            ],

            'community' => [
                [
                    'key' => 'neighbor',
                    'name_translation_key' => 'Neighbor',
                    'forward_name_translation_key' => 'Neighbor',
                    'reverse_name_translation_key' => 'Neighbor',
                    'is_directed' => false,
                ],
                [
                    'key' => 'club_member',
                    'name_translation_key' => 'Club member',
                    'forward_name_translation_key' => 'Club member',
                    'reverse_name_translation_key' => 'Club member',
                    'is_directed' => false,
                ],
                [
                    'key' => 'teammate',
                    'name_translation_key' => 'Teammate',
                    'forward_name_translation_key' => 'Teammate',
                    'reverse_name_translation_key' => 'Teammate',
                    'is_directed' => false,
                ],
            ],

            'education' => [
                [
                    'key' => 'teacher_student',
                    'name_translation_key' => 'Teacher / student',
                    'forward_name_translation_key' => 'Teacher',
                    'reverse_name_translation_key' => 'Student',
                    'is_directed' => true,
                ],
                [
                    'key' => 'classmate',
                    'name_translation_key' => 'Classmate',
                    'forward_name_translation_key' => 'Classmate',
                    'reverse_name_translation_key' => 'Classmate',
                    'is_directed' => false,
                ],
                [
                    'key' => 'tutor_student',
                    'name_translation_key' => 'Tutor / student',
                    'forward_name_translation_key' => 'Tutor',
                    'reverse_name_translation_key' => 'Student',
                    'is_directed' => true,
                ],
            ],

            'care' => [
                [
                    'key' => 'doctor_patient',
                    'name_translation_key' => 'Doctor / patient',
                    'forward_name_translation_key' => 'Doctor',
                    'reverse_name_translation_key' => 'Patient',
                    'is_directed' => true,
                ],
                [
                    'key' => 'therapist_client',
                    'name_translation_key' => 'Therapist / client',
                    'forward_name_translation_key' => 'Therapist',
                    'reverse_name_translation_key' => 'Client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'caregiver_dependent',
                    'name_translation_key' => 'Caregiver / dependent',
                    'forward_name_translation_key' => 'Caregiver',
                    'reverse_name_translation_key' => 'Dependent',
                    'is_directed' => true,
                ],
            ],

            'service' => [
                [
                    'key' => 'contractor_client',
                    'name_translation_key' => 'Contractor / client',
                    'forward_name_translation_key' => 'Contractor',
                    'reverse_name_translation_key' => 'Client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'lawyer_client',
                    'name_translation_key' => 'Lawyer / client',
                    'forward_name_translation_key' => 'Lawyer',
                    'reverse_name_translation_key' => 'Client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'accountant_client',
                    'name_translation_key' => 'Accountant / client',
                    'forward_name_translation_key' => 'Accountant',
                    'reverse_name_translation_key' => 'Client',
                    'is_directed' => true,
                ],
                [
                    'key' => 'coach_client',
                    'name_translation_key' => 'Coach / client',
                    'forward_name_translation_key' => 'Coach',
                    'reverse_name_translation_key' => 'Client',
                    'is_directed' => true,
                ],
            ],

            'household' => [
                [
                    'key' => 'roommate',
                    'name_translation_key' => 'Roommate',
                    'forward_name_translation_key' => 'Roommate',
                    'reverse_name_translation_key' => 'Roommate',
                    'is_directed' => false,
                ],
                [
                    'key' => 'landlord_tenant',
                    'name_translation_key' => 'Landlord / tenant',
                    'forward_name_translation_key' => 'Landlord',
                    'reverse_name_translation_key' => 'Tenant',
                    'is_directed' => true,
                ],
                [
                    'key' => 'household_member',
                    'name_translation_key' => 'Household member',
                    'forward_name_translation_key' => 'Household member',
                    'reverse_name_translation_key' => 'Household member',
                    'is_directed' => false,
                ],
            ],

            'online' => [
                [
                    'key' => 'online_friend',
                    'name_translation_key' => 'Online friend',
                    'forward_name_translation_key' => 'Online friend',
                    'reverse_name_translation_key' => 'Online friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'open_source_collaborator',
                    'name_translation_key' => 'Open source collaborator',
                    'forward_name_translation_key' => 'Open source collaborator',
                    'reverse_name_translation_key' => 'Open source collaborator',
                    'is_directed' => false,
                ],
                [
                    'key' => 'gaming_friend',
                    'name_translation_key' => 'Gaming friend',
                    'forward_name_translation_key' => 'Gaming friend',
                    'reverse_name_translation_key' => 'Gaming friend',
                    'is_directed' => false,
                ],
                [
                    'key' => 'follower_following',
                    'name_translation_key' => 'Follower / following',
                    'forward_name_translation_key' => 'Following',
                    'reverse_name_translation_key' => 'Follower',
                    'is_directed' => true,
                ],
            ],

            'organization' => [
                [
                    'key' => 'member',
                    'name_translation_key' => 'Member',
                    'forward_name_translation_key' => 'Member',
                    'reverse_name_translation_key' => 'Member',
                    'is_directed' => false,
                ],
                [
                    'key' => 'leader_member',
                    'name_translation_key' => 'Leader / member',
                    'forward_name_translation_key' => 'Leader',
                    'reverse_name_translation_key' => 'Member',
                    'is_directed' => true,
                ],
                [
                    'key' => 'board_member',
                    'name_translation_key' => 'Board member',
                    'forward_name_translation_key' => 'Board member',
                    'reverse_name_translation_key' => 'Board member',
                    'is_directed' => false,
                ],
            ],

            'other' => [
                [
                    'key' => 'knows',
                    'name_translation_key' => 'Knows',
                    'forward_name_translation_key' => 'Knows',
                    'reverse_name_translation_key' => 'Known by',
                    'is_directed' => true,
                ],
                [
                    'key' => 'connected_to',
                    'name_translation_key' => 'Connected to',
                    'forward_name_translation_key' => 'Connected to',
                    'reverse_name_translation_key' => 'Connected to',
                    'is_directed' => false,
                ],
                [
                    'key' => 'related_to',
                    'name_translation_key' => 'Related to',
                    'forward_name_translation_key' => 'Related to',
                    'reverse_name_translation_key' => 'Related to',
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

                $category->relationshipTypes()->insert(
                    collect($types)
                        ->values()
                        ->map(fn ($type, $index): array => [
                            'vault_id' => $this->vault->id,
                            'relationship_type_category_id' => $category->id,
                            'key' => Crypt::encryptString($type['key']),
                            'name' => null,
                            'name_translation_key' => Crypt::encryptString($type['name_translation_key']),
                            'forward_name' => null,
                            'forward_name_translation_key' => Crypt::encryptString($type['forward_name_translation_key']),
                            'reverse_name' => null,
                            'reverse_name_translation_key' => Crypt::encryptString($type['reverse_name_translation_key']),
                            'is_directed' => $type['is_directed'],
                            'can_be_deleted' => $type['can_be_deleted'] ?? true,
                            'position' => $index + 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])
                        ->all()
                );
            }
        });
    }
}
