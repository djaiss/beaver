<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\WebhookEndpointFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class WebhookEndpoint
 *
 * Represents a destination a user has registered to receive outgoing webhooks.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $label
 * @property string $url
 * @property string $secret
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class WebhookEndpoint extends Model
{
    /** @use HasFactory<WebhookEndpointFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'label',
        'url',
        'secret',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'label' => 'encrypted',
            'url' => 'encrypted',
            'secret' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the endpoint.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
