<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\IndexSearchable;
use App\Contracts\Searchable;
use App\Traits\HasAuthor;
use App\Traits\HasSearchIndex;
use Carbon\Carbon;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Location
 *
 * Where an item is physically stored, e.g. a shelf, box, or display case.
 * Locations belong to an account and can be nested.
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $parent_id
 * @property string $name
 * @property string|null $emoji
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property int|null $copies_count
 */
class Location extends Model implements Searchable
{
    use HasAuthor;

    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    use HasSearchIndex;

    protected $table = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'parent_id',
        'name',
        'emoji',
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
        ];
    }

    /**
     * Get the account the location belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the parent location, if this one is nested.
     *
     * @return BelongsTo<Location, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * Get the direct child locations.
     *
     * @return HasMany<Location, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Get the copies stored in this location.
     *
     * @return HasMany<Copy, $this>
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    public function searchableAccountId(): ?int
    {
        return $this->account_id;
    }

    /**
     * @return array<int, list<string>>
     */
    public function searchableText(): array
    {
        return [
            IndexSearchable::WEIGHT_TITLE => [$this->name],
            IndexSearchable::WEIGHT_RELATED => [(string) $this->parent?->name],
        ];
    }

    public function searchableTitle(): string
    {
        return $this->name;
    }

    public function searchableContext(): string
    {
        $copies = $this->copies_count ?? $this->copies()->count();

        return trans_choice(':count copy|:count copies', $copies, ['count' => $copies]);
    }

    /**
     * Locations have no screen of their own, so a result opens the list and
     * jumps to the row.
     */
    public function searchableUrl(): string
    {
        return route('locations.index').'#location-'.$this->id;
    }

    /**
     * @return iterable<int, Model>
     */
    public function searchableDependents(): iterable
    {
        return $this->copies()->get();
    }
}
