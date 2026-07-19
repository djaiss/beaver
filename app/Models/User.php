<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PermissionEnum;
use App\Enums\PhotoViewEnum;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id
 * @property int $account_id
 * @property string $role
 * @property bool $is_instance_administrator
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
 * @property PhotoViewEnum $photos_view
 * @property string|null $avatar_path
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
        'account_id',
        'role',
        'is_instance_administrator',
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
        'photos_view',
        'avatar_path',
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
            'photos_view' => PhotoViewEnum::class,
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
            'auto_delete_user' => 'boolean',
            'is_instance_administrator' => 'boolean',
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
     * The sizes, in CSS pixels, the avatar is displayed at in the app. Each one
     * is stored twice, at its own size and at twice that size, so a dense
     * screen has a sharp version to pick from.
     *
     * @var list<int>
     */
    public const array AVATAR_SIZES = [32, 64, 96];

    /**
     * Every pixel size actually written to disk, which is what the streaming
     * route accepts.
     *
     * @return list<int>
     */
    public static function avatarPixelSizes(): array
    {
        $sizes = collect(self::AVATAR_SIZES)
            ->flatMap(fn (int $size): array => [$size, $size * 2])
            ->unique()
            ->sort()
            ->values();

        return $sizes->all();
    }

    public function hasAvatar(): bool
    {
        return $this->avatar_path !== null;
    }

    /**
     * The path of one resized version, which sits next to the original and is
     * named after its pixel size.
     */
    public static function avatarVariantPathFor(string $path, int $pixels): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $stem = substr($path, 0, -(strlen($extension) + 1));

        return $stem.'_'.$pixels.'.'.$extension;
    }

    public function avatarVariantPath(int $pixels): string
    {
        return self::avatarVariantPathFor((string) $this->avatar_path, $pixels);
    }

    /**
     * The two versions of the avatar for a displayed size, ready for a srcset:
     * the size itself, and twice it for dense screens.
     */
    public function avatarSrcset(int $size): string
    {
        $url = fn (int $pixels): string => route('profile.avatar.show', ['user' => $this, 'size' => $pixels]);

        return $url($size).' 1x, '.$url($size * 2).' 2x';
    }

    public function avatarUrl(int $size): string
    {
        return route('profile.avatar.show', ['user' => $this, 'size' => $size]);
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
     * Get the account the user belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Check if the user is an owner of their account.
     */
    public function isOwner(): bool
    {
        return $this->role === PermissionEnum::Owner->value;
    }

    /**
     * Check if the user administers the whole instance. This is orthogonal to
     * the role, which only ever applies within the user's own account: an
     * instance administrator gains nothing extra inside their own account.
     */
    public function isInstanceAdministrator(): bool
    {
        return $this->is_instance_administrator === true;
    }
}
