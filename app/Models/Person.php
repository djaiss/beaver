<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Person
 *
 * @property int $id
 * @property int $vault_id
 * @property int|null $gender_id
 * @property string|null $kids_status
 * @property string|null $slug
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $nickname
 * @property string|null $maiden_name
 * @property string|null $suffix
 * @property string|null $prefix
 * @property bool $can_be_deleted
 * @property bool $is_listed
 * @property Carbon|null $last_consulted_at
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Person extends Model
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'persons';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vault_id',
        'gender_id',
        'kids_status',
        'slug',
        'first_name',
        'middle_name',
        'last_name',
        'nickname',
        'maiden_name',
        'suffix',
        'prefix',
        'can_be_deleted',
        'is_listed',
        'last_consulted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kids_status' => 'encrypted',
            'slug' => 'encrypted',
            'first_name' => 'encrypted',
            'middle_name' => 'encrypted',
            'last_name' => 'encrypted',
            'nickname' => 'encrypted',
            'maiden_name' => 'encrypted',
            'suffix' => 'encrypted',
            'prefix' => 'encrypted',
            'can_be_deleted' => 'boolean',
            'is_listed' => 'boolean',
            'last_consulted_at' => 'datetime',
        ];
    }

    /**
     * Get the vault associated with the person.
     *
     * @return BelongsTo<Vault, $this>
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the gender associated with the person.
     *
     * @return BelongsTo<Gender, $this>
     */
    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * Get the person's full name.
     *
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $firstName = $this->first_name;
                $lastName = $this->last_name;
                $separator = $firstName && $lastName ? ' ' : '';

                return $firstName.$separator.$lastName;
            },
        );
    }
}
