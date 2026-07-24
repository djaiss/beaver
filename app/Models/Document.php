<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use App\Traits\HasAuthor;
use Carbon\Carbon;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Document
 *
 * A file, or a reference to an external one, backing a copy or one of the records
 * hanging off it: a receipt for a transaction, an appraisal report for a
 * valuation, a policy schedule for an insurance record, a certificate backing an
 * authentication, a restoration report for maintenance work.
 *
 * A document is polymorphic, so none of those tables need their own file columns:
 * it belongs to whichever record it is attached to through documentable. It
 * carries its account directly for tenant scoping, and holds either a stored file
 * (path, mime type and size) or an external URL, never neither.
 *
 * @property int $id
 * @property int $account_id
 * @property string $documentable_type
 * @property int $documentable_id
 * @property DocumentType $type
 * @property string $name
 * @property string|null $path
 * @property string|null $external_url
 * @property string|null $mime_type
 * @property int|null $size
 * @property string|null $description
 * @property Carbon|null $issued_at
 * @property string|null $reference_number
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Document extends Model
{
    use HasAuthor;

    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'documentable_type',
        'documentable_id',
        'type',
        'name',
        'path',
        'external_url',
        'mime_type',
        'size',
        'description',
        'issued_at',
        'reference_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'name' => 'encrypted',
            'external_url' => 'encrypted',
            'description' => 'encrypted',
            'reference_number' => 'encrypted',
            'size' => 'integer',
            'issued_at' => 'date',
        ];
    }

    /**
     * Get the record the document is attached to: a copy, or one of the records
     * hanging off it.
     *
     * @return MorphTo<Model, $this>
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the account the document belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Only the documents of one account.
     *
     * @param  Builder<Document>  $query
     * @return Builder<Document>
     */
    #[Scope]
    protected function ofAccount(Builder $query, Account $account): Builder
    {
        return $query->where('account_id', $account->id);
    }

    /**
     * Get the item the document ultimately hangs from, whichever record it is
     * attached to. A copy reaches its item directly; every other documentable
     * reaches it through its copy. The item log is keyed by item, so this is what
     * an upload or a deletion is recorded against.
     */
    public function item(): Item
    {
        $documentable = $this->documentable;

        if ($documentable instanceof Copy) {
            return $documentable->item;
        }

        /** @var Copy $copy */
        $copy = $documentable->getRelationValue('copy');

        return $copy->item;
    }

    /**
     * Whether the document is a file held on our disk rather than a link.
     */
    public function isStored(): bool
    {
        return $this->path !== null;
    }

    /**
     * Whether the document is a link to a file held elsewhere.
     */
    public function isExternal(): bool
    {
        return $this->path === null && $this->external_url !== null;
    }

    /**
     * Where the document can be reached: the streamed download for a stored file,
     * or the external link for one held elsewhere.
     */
    public function url(): ?string
    {
        if ($this->isStored()) {
            return route('documents.show', $this);
        }

        return $this->external_url;
    }

    /**
     * The mime type reduced to the label a person recognises, e.g. "PDF". An
     * external document has no file, so it has no format.
     */
    public function format(): ?string
    {
        if ($this->mime_type === null) {
            return null;
        }

        $separator = mb_strpos($this->mime_type, '/');

        if ($separator === false) {
            return mb_strtoupper($this->mime_type);
        }

        return mb_strtoupper(mb_substr($this->mime_type, $separator + 1));
    }

    /**
     * The stored file size in the largest unit that keeps it readable, e.g.
     * "2.4 MB". An external document has no file, so it has no size.
     */
    public function humanSize(): ?string
    {
        if ($this->size === null) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, $unit === 0 ? 0 : 1).' '.$units[$unit];
    }
}
