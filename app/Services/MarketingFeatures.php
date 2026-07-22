<?php

declare(strict_types=1);

namespace App\Services;

/**
 * The catalogue of feature areas shown in the marketing "Features" mega menu and
 * on the public /features pages. It lives in one place so the navigation popover,
 * the features hub and the individual feature pages never drift apart. The copy is
 * resolved through __() at call time, so it stays correct per locale (unlike a
 * cached config file).
 */
class MarketingFeatures
{
    /**
     * The three grouped columns of feature areas, in display order.
     *
     * @return array<int, array{label: string, dot: string, items: array<int, array{slug: string, title: string, desc: string, dot: string, iconRadius: string, isNew: bool}>}>
     */
    public function columns(): array
    {
        return [
            [
                'label' => __('Track & understand'),
                'dot' => '#3b82f6',
                'items' => [
                    $this->feature('copy-tracking', __('Copy tracking'), __('Distinguish, locate, and understand every physical item you own.'), '#3b82f6'),
                    $this->feature('copy-history', __('Copy history'), __('Keep money, custody, care, provenance, and paperwork together.'), '#6366f1'),
                    $this->feature('custom-catalogues', __('Custom catalogues'), __('Record the details that matter to comics, watches, wine, cards, and more.'), '#8b5cf6', iconRadius: '3px', isNew: true),
                    $this->feature('collection-insights', __('Collection insights'), __('Turn recorded facts into trusted value and growth insights.'), '#0ea5e9'),
                ],
            ],
            [
                'label' => __('Own & collaborate'),
                'dot' => '#ec4899',
                'items' => [
                    $this->feature('data-ownership', __('Data ownership'), __('Self-host, encrypt, back up, and keep ownership of your data.'), '#ec4899', iconRadius: '9999px'),
                    $this->feature('collaboration', __('Collaboration'), __('Give a household, club, or team access without losing accountability.'), '#f43f5e', iconRadius: '9999px'),
                    $this->feature('organization', __('Organization'), __('Browse visually or compare precisely with tools that scale.'), '#fb923c'),
                    $this->feature('photos-and-browsing', __('Photos & browsing'), __('Manage covers, photo libraries, and visual browsing.'), '#f59e0b'),
                ],
            ],
            [
                'label' => __('Protect & extend'),
                'dot' => '#34d399',
                'items' => [
                    $this->feature('protection-and-care', __('Protection & care'), __('Record insurance, service, condition, and storage context.'), '#34d399'),
                    $this->feature('api', __('API'), __('Build on your catalogue with the complete JSON API and personal keys.'), '#10b981', iconRadius: '3px'),
                    $this->feature('self-hosting', __('Self-hosting'), __('Install and operate a full instance with Docker.'), '#14b8a6'),
                    $this->feature('security', __('Security'), __('Encryption at rest, 2FA, magic links, recovery codes, and alerts.'), '#64748b', iconRadius: '9999px'),
                ],
            ],
        ];
    }

    /**
     * A flat list of every feature area, useful for the hub page and for
     * validating a slug.
     *
     * @return array<int, array{slug: string, title: string, desc: string, dot: string, iconRadius: string, isNew: bool}>
     */
    public function all(): array
    {
        return collect($this->columns())->flatMap(fn (array $column): array => $column['items'])->all();
    }

    /**
     * The single feature area matching the slug, or null when none does.
     *
     * @return array{slug: string, title: string, desc: string, dot: string, iconRadius: string, isNew: bool}|null
     */
    public function find(string $slug): ?array
    {
        return collect($this->all())->firstWhere('slug', $slug);
    }

    /**
     * @return array{slug: string, title: string, desc: string, dot: string, iconRadius: string, isNew: bool}
     */
    private function feature(string $slug, string $title, string $desc, string $dot, string $iconRadius = '4px', bool $isNew = false): array
    {
        return [
            'slug' => $slug,
            'title' => $title,
            'desc' => $desc,
            'dot' => $dot,
            'iconRadius' => $iconRadius,
            'isNew' => $isNew,
        ];
    }
}
