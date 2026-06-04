<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Gender
 *
 * @property int $id
 * @property int $vault_id
 * @property string|null $name
 * @property string|null $name_translation_key
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Gender extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'genders';

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
     * Get the vault associated with the gender.
     *
     * @return BelongsTo<Vault, $this>
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the display name of the gender.
     * Returns the name field if set, otherwise returns the translated value of
     * name_translation_key.
     */
    public function getName(): string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        return __($this->name_translation_key ?? '');
    }
}
