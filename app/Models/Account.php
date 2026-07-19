<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PermissionEnum;
use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Account
 *
 * @property int $id
 * @property string $name
 * @property string $currency_code
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Account extends Model
{
    use HasAuthor;

    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    protected $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'currency_code',
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
     * Get the users who belong to the account.
     *
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the pending and past invitations of the account.
     *
     * @return HasMany<Invitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get the collections that belong to the account.
     *
     * @return HasMany<Collection, $this>
     */
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Get the collection types that belong to the account.
     *
     * @return HasMany<CollectionType, $this>
     */
    public function collectionTypes(): HasMany
    {
        return $this->hasMany(CollectionType::class);
    }

    /**
     * Get the locations that belong to the account.
     *
     * @return HasMany<Location, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get the conditions that belong to the account, excluding the system defaults.
     *
     * @return HasMany<Condition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class);
    }

    /**
     * Get the tags that belong to the account.
     *
     * @return HasMany<Tag, $this>
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Get the series that belong to the account.
     *
     * @return HasMany<Series, $this>
     */
    public function series(): HasMany
    {
        return $this->hasMany(Series::class);
    }

    /**
     * Get the sets that belong to the account, through the collections they live in.
     *
     * @return HasManyThrough<Set, Collection, $this>
     */
    public function sets(): HasManyThrough
    {
        return $this->hasManyThrough(Set::class, Collection::class);
    }

    /**
     * Get the users who administer the account.
     *
     * @return HasMany<User, $this>
     */
    public function administrators(): HasMany
    {
        return $this->users()->where('role', PermissionEnum::Owner->value);
    }

    /**
     * Check whether the given user belongs to the account.
     */
    public function hasMember(User $user): bool
    {
        return $user->account_id === $this->id;
    }

    /**
     * Return the role the given user holds in the account, if any.
     */
    public function roleFor(User $user): ?string
    {
        return $user->account_id === $this->id ? $user->role : null;
    }

    /**
     * Whether the user may manage the account's content: owners and editors
     * can, viewers and non-members cannot.
     */
    public function allowsManagementBy(User $user): bool
    {
        return in_array(
            $this->roleFor($user),
            [PermissionEnum::Owner->value, PermissionEnum::Editor->value],
            true,
        );
    }
}
