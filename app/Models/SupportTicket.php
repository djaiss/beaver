<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SupportCategory;
use App\Enums\SupportTicketCloser;
use App\Enums\SupportTicketStatus;
use Carbon\Carbon;
use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class SupportTicket
 *
 * A conversation a user opened with the people running the instance. It groups
 * the messages of one thread and remembers what it is about and whether it is
 * still open.
 *
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property SupportCategory $category
 * @property SupportTicketStatus $status
 * @property SupportTicketCloser|null $closed_by
 * @property Carbon|null $closed_at
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 * @property Collection<int, SupportMessage> $messages
 */
class SupportTicket extends Model
{
    /** @use HasFactory<SupportTicketFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'subject',
        'category',
        'status',
        'closed_by',
        'closed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subject' => 'encrypted',
            'category' => SupportCategory::class,
            'status' => SupportTicketStatus::class,
            'closed_by' => SupportTicketCloser::class,
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Get the user that opened the conversation.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages of the conversation.
     *
     * @return HasMany<SupportMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }
}
