<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $nickname
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $last_activity_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property array|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $last_used_ip
 * @property Carbon|null $trial_ends_at
 * @property string $locale
 * @property bool $time_format_24h
 * @property bool $auto_delete_user
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'nickname',
        'email',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'email_verified_at',
        'trial_ends_at',
        'last_used_ip',
        'last_activity_at',
        'locale',
        'time_format_24h',
        'auto_delete_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'first_name' => 'encrypted',
            'last_name' => 'encrypted',
            'nickname' => 'encrypted',
            'last_used_ip' => 'encrypted',
            'email_verified_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_at' => 'datetime',
            'time_format_24h' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
            'auto_delete_user' => 'boolean',
        ];
    }

    /**
     * Get the emailsSent associated with the user.
     *
     * @return HasMany<EmailSent, $this>
     */
    public function emailsSent(): HasMany
    {
        return $this->hasMany(EmailSent::class);
    }

    /**
     * Get the webhook endpoints associated with the user.
     *
     * @return HasMany<WebhookEndpoint, $this>
     */
    public function webhookEndpoints(): HasMany
    {
        return $this->hasMany(WebhookEndpoint::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->first_name.' '.$this->last_name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the user's full name by combining first and last name.
     */
    public function getFullName(): string
    {
        $firstName = $this->first_name;
        $lastName = $this->last_name;
        $separator = $firstName && $lastName ? ' ' : '';

        return $firstName.$separator.$lastName;
    }

    /**
     * Get the accounts the user is a member of.
     *
     * @return BelongsToMany<Account, $this>
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_user')
            ->withPivot('role', 'invited_by', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the account membership rows of the user.
     *
     * @return HasMany<AccountMember, $this>
     */
    public function accountMemberships(): HasMany
    {
        return $this->hasMany(AccountMember::class);
    }

    /**
     * Check if the user is a member of a specific account.
     */
    public function isMemberOf(Account $account): bool
    {
        return $this->accountMemberships()->where('account_id', $account->id)->exists();
    }

    /**
     * Return the membership object for the user in the given account.
     */
    public function memberFor(Account $account): ?AccountMember
    {
        return $this->accountMemberships()->where('account_id', $account->id)->first();
    }

    /**
     * Return the role the user holds in the given account, if any.
     */
    public function roleOn(Account $account): ?string
    {
        return $this->accountMemberships()->where('account_id', $account->id)->value('role');
    }
}
