<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PermissionEnum;
use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Account
 *
 * @property int $id
 * @property string $name
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
     * Get the users who are members of the account.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_user')
            ->withPivot('role', 'invited_by', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the membership rows of the account.
     *
     * @return HasMany<AccountMember, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(AccountMember::class);
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
     * Get the users who administer the account.
     *
     * @return BelongsToMany<User, $this>
     */
    public function administrators(): BelongsToMany
    {
        return $this->users()->wherePivot('role', PermissionEnum::Owner->value);
    }

    /**
     * Check whether the given user is a member of the account.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Return the role the given user holds in the account, if any.
     */
    public function roleFor(User $user): ?string
    {
        return $this->members()->where('user_id', $user->id)->value('role');
    }
}
