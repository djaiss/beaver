<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\SiteOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class SiteOption
 *
 * The instance wide options an administrator sets from the panel. There is only
 * ever one row: the panel edits it, and the marketing site reads it.
 *
 * @property int $id
 * @property bool $banner_enabled
 * @property string|null $banner_version
 * @property string|null $banner_url
 * @property array<string, array<string, string|null>>|null $banner_content
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class SiteOption extends Model
{
    /** @use HasFactory<SiteOptionFactory> */
    use HasFactory;

    protected $table = 'site_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'banner_enabled',
        'banner_version',
        'banner_url',
        'banner_content',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'banner_enabled' => 'boolean',
            'banner_version' => 'encrypted',
            'banner_url' => 'encrypted',
            'banner_content' => 'encrypted:array',
        ];
    }

    /**
     * The one row, or an unsaved instance when nothing has been saved yet, so
     * the marketing site works on a fresh install.
     */
    public static function current(): self
    {
        return self::query()->first() ?? new self;
    }

    /**
     * The banner to show in the given locale, or null when there is nothing to
     * show. Each field falls back to English on its own, so a locale that has
     * only been half translated still gets a complete banner.
     *
     * @return array{version: string|null, url: string|null, text: string, link_label: string|null}|null
     */
    public function bannerFor(string $locale): ?array
    {
        if (! $this->banner_enabled) {
            return null;
        }

        $text = $this->contentValue($locale, 'text');

        if ($text === null) {
            return null;
        }

        $url = $this->safeBannerUrl();

        return [
            'version' => $this->banner_version,
            'url' => $url,
            'text' => $text,
            'link_label' => $url === null ? null : $this->contentValue($locale, 'link_label'),
        ];
    }

    /**
     * One field of the per locale content, falling back to English and then to
     * null when neither has been filled in.
     */
    private function contentValue(string $locale, string $field): ?string
    {
        foreach ([$locale, 'en'] as $key) {
            $value = trim((string) ($this->banner_content[$key][$field] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * The banner URL only if it is a safe http(s) one, so a tampered or
     * malformed scheme (javascript:, data:) is never rendered as an anchor.
     * Validation gates this on the way in; this is the belt-and-braces on the
     * way out.
     */
    private function safeBannerUrl(): ?string
    {
        if ($this->banner_url === null) {
            return null;
        }

        return Str::startsWith($this->banner_url, ['http://', 'https://']) ? $this->banner_url : null;
    }
}
