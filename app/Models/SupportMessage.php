<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\SupportMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SupportMessage
 *
 * A single message within a support conversation. A message is written either by
 * the user who owns the ticket or, when `is_from_team` is true, by the instance
 * team replying from the administration panel.
 *
 * @property int $id
 * @property int $support_ticket_id
 * @property int $user_id
 * @property bool $is_from_team
 * @property string $body
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property SupportTicket $ticket
 * @property User $user
 */
class SupportMessage extends Model
{
    /** @use HasFactory<SupportMessageFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'is_from_team',
        'body',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_from_team' => 'boolean',
            'body' => 'encrypted',
        ];
    }

    /**
     * Get the conversation the message belongs to.
     *
     * @return BelongsTo<SupportTicket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    /**
     * Get the user that wrote the message.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
