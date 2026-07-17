<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\CollectionTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CollectionType
 *
 * A user-defined category (e.g. Comics, Vinyl, Wine) that drives which custom
 * fields apply to an item. Stored in the "types" table.
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string $color
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class CollectionType extends Model
{
    use HasAuthor;

    /** @use HasFactory<CollectionTypeFactory> */
    use HasFactory;

    protected $table = 'types';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'color',
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
     * Get the account the type belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the custom fields defined on the type.
     *
     * @return HasMany<CustomField, $this>
     */
    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class, 'type_id');
    }

    /**
     * Get the custom fields that sit outside of any group.
     *
     * @return HasMany<CustomField, $this>
     */
    public function ungroupedCustomFields(): HasMany
    {
        return $this->customFields()->whereNull('group_id');
    }

    /**
     * Get the groups of custom fields defined on the type.
     *
     * @return HasMany<CustomFieldGroup, $this>
     */
    public function customFieldGroups(): HasMany
    {
        return $this->hasMany(CustomFieldGroup::class, 'type_id');
    }

    /**
     * Get the collections the type is linked to.
     *
     * @return BelongsToMany<Collection, $this>
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_type', 'type_id', 'collection_id');
    }
}
