<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\LocationHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LocationHistory
 *
 * Where a copy was physically stored during a period of time. It turns the copy's
 * single current-location pointer into a record of movement: a copy gathers a row
 * each time it moves, the open one (with no moved_out_at) being where it is now.
 * A copy has at most one open record at a time, and copies.current_location_id
 * always mirrors that open record's location.
 *
 * @property int $id
 * @property int $copy_id
 * @property int|null $location_id
 * @property Carbon $moved_at
 * @property Carbon|null $moved_out_at
 * @property string|null $reason
 * @property string|null $note
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class LocationHistory extends Model
{
    use HasAuthor;

    /** @use HasFactory<LocationHistoryFactory> */
    use HasFactory;

    protected $table = 'location_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'location_id',
        'moved_at',
        'moved_out_at',
        'reason',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'moved_at' => 'date',
            'moved_out_at' => 'date',
            'reason' => 'encrypted',
            'note' => 'encrypted',
        ];
    }

    /**
     * Get the copy this move belongs to.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Get the location the copy was stored at, if it still exists.
     *
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Whether this is the open record, the place the copy is now.
     */
    public function isOpen(): bool
    {
        return $this->moved_out_at === null;
    }
}
