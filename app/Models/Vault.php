<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\GenerateVaultAvatar;
use Carbon\Carbon;
use Database\Factories\VaultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Vault
 *
 * @property int $id
 * @property string $name
 * @property string|null $invitation_code
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Vault extends Model
{
    /** @use HasFactory<VaultFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vaults';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'invitation_code',
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
     * Get the members of the vault.
     *
     * @return HasMany<Member, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Gets the avatar of the vault.
     */
    public function getAvatar(): string
    {
        return new GenerateVaultAvatar($this->id.'-'.$this->name)->execute();
    }
}
