<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\MaritalStatusFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class MaritalStatus
 *
 * @property int $id
 * @property int $vault_id
 * @property string|null $name
 * @property string|null $name_translation_key
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class MaritalStatus extends Model
{
    /** @use HasFactory<MaritalStatusFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marital_statuses';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vault_id',
        'name',
        'name_translation_key',
        'position',
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
            'name_translation_key' => 'encrypted',
            'position' => 'integer',
        ];
    }

    /**
     * Get the vault associated with the marital status.
     *
     * @return BelongsTo<Vault, $this>
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the persons associated with the marital status.
     *
     * @return HasMany<Person, $this>
     */
    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    /**
     * Get the display name of the marital status.
     */
    public function getName(): string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return __($this->name_translation_key ?? '');
    }
}
