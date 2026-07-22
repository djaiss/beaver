<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TestimonialStatus;
use Carbon\Carbon;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Class Testimonial
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $link
 * @property string $body
 * @property TestimonialStatus $status
 * @property Carbon|null $submitted_at
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    protected $table = 'testimonials';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'link',
        'body',
        'status',
        'submitted_at',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'link' => 'encrypted',
            'body' => 'encrypted',
            'status' => TestimonialStatus::class,
            'submitted_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the member who wrote the testimonial.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The published testimonials, most recently published first, as the public
     * marketing pages show them.
     *
     * @param  Builder<Testimonial>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', TestimonialStatus::Published)
            ->orderByDesc('published_at');
    }

    /**
     * The first letter of the display name, for the avatar bubble.
     */
    public function initial(): string
    {
        return Str::upper(Str::substr(trim($this->name), 0, 1)) ?: 'K';
    }

    /**
     * The link only if it is a safe http(s) URL, so a tampered or malformed
     * scheme (javascript:, data:) is never rendered as an anchor. Validation
     * gates this on the way in; this is the belt-and-braces on the way out.
     */
    public function safeLink(): ?string
    {
        if ($this->link === null) {
            return null;
        }

        return Str::startsWith($this->link, ['http://', 'https://']) ? $this->link : null;
    }
}
