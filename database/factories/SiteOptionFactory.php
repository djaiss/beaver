<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SiteOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteOption>
 */
class SiteOptionFactory extends Factory
{
    protected $model = SiteOption::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'banner_enabled' => false,
            'banner_version' => null,
            'banner_url' => null,
            'banner_content' => null,
        ];
    }

    public function withBanner(): static
    {
        return $this->state(fn (): array => [
            'banner_enabled' => true,
            'banner_version' => 'v0.9',
            'banner_url' => 'https://github.com/djaiss/kollek/releases',
            'banner_content' => [
                'en' => [
                    'text' => 'Custom item types are here. Build a schema for any hobby.',
                    'link_label' => 'Read the changelog',
                ],
            ],
        ]);
    }
}
