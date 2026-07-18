<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemActionEnum;
use Carbon\Carbon;
use Database\Factories\ItemLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ItemLog
 *
 * One entry of the activity trail of a single item. The logs table next to it
 * holds the same actions from the point of view of the user who performed
 * them, and feeds the dashboard instead.
 *
 * @property int $id
 * @property int $item_id
 * @property int|null $user_id
 * @property string $user_name
 * @property string $action
 * @property array|null $parameters
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ItemLog extends Model
{
    /** @use HasFactory<ItemLogFactory> */
    use HasFactory;

    protected $table = 'item_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'user_name',
        'action',
        'parameters',
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
            'action' => 'encrypted',
            'parameters' => 'array',
        ];
    }

    /**
     * Get the item the action was performed on.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the user who performed the action, if they still exist.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the name of the user who performed the action. A user who has since
     * been deleted leaves the name captured when the entry was written.
     */
    public function getUserName(): string
    {
        return $this->user ? $this->user->getFullName() : $this->user_name;
    }

    /**
     * Get the translated sentence for this entry, without its chips.
     */
    public function getTranslatedDescription(): string
    {
        $translationKey = ItemActionEnum::tryFrom($this->action)?->translationKey() ?? $this->action;

        return __($translationKey);
    }

    /**
     * Get the chips that sit after the sentence, each with the style it is
     * rendered in.
     *
     * A `label` chip names something the action applied to, such as a tag. A
     * `file` chip names an uploaded file. A `changes` entry becomes one chip
     * per value that moved, reading "Grade: CGC 3.5 → CGC 4.0", or just the
     * label when the values are too long to be worth showing.
     *
     * @return list<array{style: string, label: string}>
     */
    public function getChips(): array
    {
        $parameters = $this->parameters ?? [];
        $chips = [];

        if (isset($parameters['label'])) {
            $chips[] = ['style' => 'plain', 'label' => (string) $parameters['label']];
        }

        if (isset($parameters['file'])) {
            $chips[] = ['style' => 'file', 'label' => (string) $parameters['file']];
        }

        foreach ($parameters['changes'] ?? [] as $change) {
            $chips[] = ['style' => 'mono', 'label' => $this->describeChange($change)];
        }

        return $chips;
    }

    /**
     * @param  array{label: string, from?: string|null, to?: string|null}  $change
     */
    private function describeChange(array $change): string
    {
        $label = __($change['label']);
        $from = $change['from'] ?? null;
        $to = $change['to'] ?? null;

        if ($from === null && $to === null) {
            return $label;
        }

        return $label.': '.($from ?? __('empty')).' → '.($to ?? __('empty'));
    }
}
