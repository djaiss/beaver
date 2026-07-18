<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Builds the content of the marketing homepage.
 *
 * Everything here is placeholder copy and placeholder numbers: the homepage does not read
 * from the database yet. Keeping it in one class means the controller stays thin and the
 * fake data has a single obvious place to be replaced with the real thing later.
 */
class MarketingHomepage
{
    /**
     * The pastel pairs used by the generated cover artwork of a collection.
     *
     * @var array<int, array{0: string, 1: string}>
     */
    private array $palette = [
        ['#fb923c', '#fdba74'],
        ['#8b5cf6', '#c4b5fd'],
        ['#34d399', '#6ee7b7'],
        ['#ec4899', '#f9a8d4'],
        ['#3b82f6', '#93c5fd'],
    ];

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return [
            'announcement' => $this->announcement(),
            'dashboard' => $this->dashboard(),
            'trustCards' => $this->trustCards(),
            'organizeFeatures' => $this->organizeFeatures(),
            'itemTypes' => $this->itemTypes(),
            'copies' => $this->copies(),
            'copyFields' => $this->copyFields(),
            'supported' => $this->supported(),
            'openSourcePoints' => $this->openSourcePoints(),
            'githubStats' => $this->githubStats(),
            'selfHostFeatures' => $this->selfHostFeatures(),
            'cloudFeatures' => $this->cloudFeatures(),
            'shipped' => $this->shipped(),
            'coming' => $this->coming(),
            'faqs' => $this->faqs(),
            'footerColumns' => $this->footerColumns(),
        ];
    }

    /**
     * @return array{version: string, text: string}
     */
    private function announcement(): array
    {
        return [
            'version' => 'v0.9',
            'text' => 'Custom item types are here. Build a schema for any hobby.',
        ];
    }

    /**
     * The product screenshot rendered as markup in the hero.
     *
     * @return array<string, mixed>
     */
    private function dashboard(): array
    {
        return [
            'greeting' => 'Good afternoon, Phoebe',
            'navigation' => [
                ['label' => 'Dashboard', 'dot' => '#3b82f6', 'active' => true],
                ['label' => 'Search', 'dot' => '#6b7280', 'active' => false],
                ['label' => 'Collections', 'dot' => '#8b5cf6', 'active' => false],
                ['label' => 'Locations', 'dot' => '#34d399', 'active' => false],
            ],
            'collections' => [
                ['name' => 'Marvel Comics', 'dot' => '#fb923c'],
                ['name' => 'Jazz LPs', 'dot' => '#8b5cf6'],
                ['name' => 'Wine Cellar', 'dot' => '#ec4899'],
            ],
            'stats' => [
                ['label' => 'Total items', 'value' => '567', 'delta' => '+18'],
                ['label' => 'Est. value', 'value' => '$20.3k', 'delta' => '+$1.2k'],
                ['label' => 'Collections', 'value' => '4', 'delta' => '3 members'],
                ['label' => 'Sets', 'value' => '3', 'delta' => 'in progress'],
            ],
            'cards' => [
                ['name' => 'Marvel Comics 1990s', 'meta' => '142 items · $8,420', 'from' => '#fb923c', 'to' => '#fdba74'],
                ['name' => 'Vinyl — Jazz LPs', 'meta' => '67 items · $3,150', 'from' => '#8b5cf6', 'to' => '#c4b5fd'],
                ['name' => 'Trading Cards', 'meta' => '310 items · $5,980', 'from' => '#34d399', 'to' => '#6ee7b7'],
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, icon: string, items: array<int, string>, description: string}>
     */
    private function trustCards(): array
    {
        return [
            [
                'title' => 'Any collection',
                'icon' => 'library-big',
                'items' => ['Books', 'Comics', 'Vinyl', 'Wine', 'Coins', 'Games'],
                'description' => 'One tool for every hobby. Collect anything, and the app adapts to it.',
            ],
            [
                'title' => 'Your data',
                'icon' => 'database',
                'items' => ['Import', 'Export', 'Self-host', 'Keep it'],
                'description' => 'Open schema, one click export, no lock-in. Keep your catalog forever.',
            ],
            [
                'title' => 'Open source',
                'icon' => 'git-fork',
                'items' => ['MIT', 'Transparent', 'Community', 'Built to last'],
                'description' => 'The whole source, in the open. Auditable, forkable, community driven.',
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, description: string, dot: string}>
     */
    private function organizeFeatures(): array
    {
        return [
            ['title' => 'Unlimited collections', 'description' => 'No caps, no tiers gating how much you can catalog.', 'dot' => '#3b82f6'],
            ['title' => 'Nested categories', 'description' => 'Marvel, then Spider-Man, then 1990s. Structure as deep as you need.', 'dot' => '#8b5cf6'],
            ['title' => 'Tags', 'description' => 'Cross-cut your catalog with flexible, colour coded labels.', 'dot' => '#ec4899'],
            ['title' => 'Nested locations', 'description' => 'Room, shelf, box. Always know where a piece lives.', 'dot' => '#34d399'],
            ['title' => 'Conditions', 'description' => 'Grade every item on the scale that fits your hobby.', 'dot' => '#fb923c'],
            ['title' => 'Beautiful item pages', 'description' => 'Rich pages that make a catalog feel like a museum.', 'dot' => '#111111'],
        ];
    }

    /**
     * @return array<int, array{name: string, fields: array<int, array{name: string, type: string, sample: string}>}>
     */
    private function itemTypes(): array
    {
        return [
            [
                'name' => 'Books',
                'fields' => [
                    ['name' => 'Author', 'type' => 'txt', 'sample' => 'Le Guin, U.'],
                    ['name' => 'ISBN', 'type' => '#', 'sample' => '978-0-441…'],
                    ['name' => 'Publisher', 'type' => 'txt', 'sample' => 'Ace Books'],
                    ['name' => 'First edition', 'type' => '☑', 'sample' => 'true'],
                    ['name' => 'Pages', 'type' => '#', 'sample' => '304'],
                ],
            ],
            [
                'name' => 'Wine',
                'fields' => [
                    ['name' => 'Vintage', 'type' => '#', 'sample' => '2015'],
                    ['name' => 'Region', 'type' => 'txt', 'sample' => 'Barolo'],
                    ['name' => 'Winery', 'type' => 'txt', 'sample' => 'G. Conterno'],
                    ['name' => 'Drink by', 'type' => 'date', 'sample' => '2035'],
                    ['name' => 'Bottles', 'type' => '#', 'sample' => '6'],
                ],
            ],
            [
                'name' => 'Comics',
                'fields' => [
                    ['name' => 'Issue', 'type' => '#', 'sample' => '#300'],
                    ['name' => 'Publisher', 'type' => 'txt', 'sample' => 'Marvel'],
                    ['name' => 'Grade', 'type' => 'txt', 'sample' => 'CGC 9.8'],
                    ['name' => 'Key issue', 'type' => '☑', 'sample' => 'true'],
                    ['name' => 'Writer', 'type' => 'txt', 'sample' => 'Michelinie'],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function copies(): array
    {
        return [
            ['condition' => 'Near Mint', 'location' => 'Display Case', 'added' => 'Aug 2023', 'value' => '$640', 'paid' => '$420', 'from' => '#fb923c', 'to' => '#fdba74'],
            ['condition' => 'Very Fine', 'location' => 'Box A1', 'added' => 'Jan 2023', 'value' => '$180', 'paid' => '$120', 'from' => '#8b5cf6', 'to' => '#c4b5fd'],
            ['condition' => 'Good', 'location' => 'Box B1', 'added' => 'Jun 2023', 'value' => '$95', 'paid' => '$60', 'from' => '#34d399', 'to' => '#6ee7b7'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function copyFields(): array
    {
        return ['Purchase date', 'Price paid', 'Estimated value', 'Condition', 'Location', 'Provenance'];
    }

    /**
     * @return array<int, array{name: string, from: string, to: string}>
     */
    private function supported(): array
    {
        $names = ['Books', 'Comics', 'Manga', 'Vinyl Records', 'CDs', 'DVDs', 'Blu-rays', 'Video Games', 'Trading Cards', 'Coins', 'Stamps', 'Wine', 'Watches', 'Toys', 'Action Figures'];

        return collect($names)
            ->map(function (string $name, int $index): array {
                $pair = $this->palette[$index % count($this->palette)];

                return ['name' => $name, 'from' => $pair[0], 'to' => $pair[1]];
            })
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function openSourcePoints(): array
    {
        return ['No vendor lock-in', 'Full source code', 'Self-hosting, always free', 'Commercial use allowed', 'Community contributions'];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function githubStats(): array
    {
        return [
            ['value' => '4.8k', 'label' => 'Stars'],
            ['value' => '112', 'label' => 'Contributors'],
            ['value' => '3.4k', 'label' => 'Commits'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function selfHostFeatures(): array
    {
        return ['Free forever', 'One command Docker deploy', 'Full control of your data', 'Unlimited customization'];
    }

    /**
     * @return array<int, string>
     */
    private function cloudFeatures(): array
    {
        return ['One payment, no renewals', 'Lifetime managed hosting', 'Automatic updates and backups', 'Zero maintenance'];
    }

    /**
     * @return array<int, string>
     */
    private function shipped(): array
    {
        return ['Accounts & members', 'Collections', 'Custom item types', 'Custom fields', 'Nested locations', 'Conditions'];
    }

    /**
     * @return array<int, string>
     */
    private function coming(): array
    {
        return ['Sets & completion', 'Provenance history', 'Value history', 'Barcode scanning', 'Mobile apps', 'Import tools'];
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    private function faqs(): array
    {
        $name = config('app.name');

        return [
            [
                'question' => 'Can I really self-host it?',
                'answer' => $name.' ships as a single container. One docker run and you have the full application on your own hardware, with your data on a volume you control.',
            ],
            [
                'question' => 'Is '.$name.' free?',
                'answer' => 'Self-hosting is free forever under the MIT License. The optional managed cloud is a single payment for people who would rather not run the ops.',
            ],
            [
                'question' => 'Who owns my data?',
                'answer' => 'You do. There is no proprietary format and no lock-in. Everything is stored in an open schema you can inspect, back up, and take with you.',
            ],
            [
                'question' => 'Can I export everything?',
                'answer' => 'One click export of your entire catalog to JSON, including custom types and every physical copy. Import it back into any instance.',
            ],
            [
                'question' => 'Is there an API?',
                'answer' => 'Yes. A documented REST API covers every collection, item, and custom field, so you can script imports, sync tools, or build your own front end.',
            ],
            [
                'question' => 'Can I contribute?',
                'answer' => 'Absolutely, that is the point of MIT. Open an issue, send a pull request, or fork it entirely. The roadmap is public and community driven.',
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, links: array<int, array{label: string, url: string}>}>
     */
    private function footerColumns(): array
    {
        return [
            [
                'title' => 'Product',
                'links' => [
                    ['label' => 'Features', 'url' => '#features'],
                    ['label' => 'Roadmap', 'url' => '#roadmap'],
                    ['label' => 'Pricing', 'url' => '#pricing'],
                ],
            ],
            [
                'title' => 'Resources',
                'links' => [
                    ['label' => 'API reference', 'url' => route('marketing.docs.api.index')],
                    ['label' => 'FAQ', 'url' => '#faq'],
                    ['label' => 'Changelog', 'url' => config('marketing.github_url').'/releases'],
                ],
            ],
            [
                'title' => 'Community',
                'links' => [
                    ['label' => 'GitHub', 'url' => config('marketing.github_url')],
                    ['label' => 'Discussions', 'url' => config('marketing.github_url').'/discussions'],
                    ['label' => 'Issues', 'url' => config('marketing.github_url').'/issues'],
                ],
            ],
            [
                'title' => 'Legal',
                'links' => [
                    ['label' => 'Privacy', 'url' => '#'],
                    ['label' => 'Terms', 'url' => '#'],
                    ['label' => 'MIT License', 'url' => config('marketing.github_url').'/blob/main/LICENSE'],
                ],
            ],
        ];
    }
}
