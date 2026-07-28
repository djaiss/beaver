<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PurchaseConsentChoice;
use Carbon\Carbon;
use Database\Factories\PurchaseConsentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PurchaseConsent
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $user_id
 * @property string $user_name
 * @property PurchaseConsentChoice $choice
 * @property string|null $ip_address
 * @property Carbon $accepted_at
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Account $account
 * @property User|null $user
 */
class PurchaseConsent extends Model
{
    /** @use HasFactory<PurchaseConsentFactory> */
    use HasFactory;

    protected $table = 'purchase_consents';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'user_id',
        'user_name',
        'choice',
        'ip_address',
        'accepted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_name' => 'encrypted',
            'ip_address' => 'encrypted',
            'choice' => PurchaseConsentChoice::class,
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * Get the account the confirmation was given for.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the user who gave the confirmation, if they still exist.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The name of the person who confirmed. The row outlives them, so the name
     * recorded at the time is what is left once the user is gone.
     */
    public function getUserName(): string
    {
        return $this->user ? $this->user->getFullName() : $this->user_name;
    }
}
