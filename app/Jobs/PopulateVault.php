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
}
