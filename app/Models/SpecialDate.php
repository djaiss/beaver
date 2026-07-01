<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\SpecialDateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SpecialDate
 *
 * @property int $id
 * @property int $vault_id
 * @property int $person_id
 * @property bool $should_be_reminded
 * @property int|null $year
 * @property int|null $month
 * @property int|null $day
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class SpecialDate extends Model
{
    /** @use HasFactory<SpecialDateFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'vault_id',
        'person_id',
        'should_be_reminded',
        'year',
        'month',
        'day',
        'name',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'should_be_reminded' => false,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'should_be_reminded' => 'boolean',
            'year' => 'integer',
            'month' => 'integer',
            'day' => 'integer',
            'name' => 'encrypted',
        ];
    }

    /** @return BelongsTo<Vault, $this> */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /** @return BelongsTo<Person, $this> */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
