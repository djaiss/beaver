<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Add a photo to an item. The first photo of an item becomes its main visual,
 * and later ones are appended after the existing photos. Only owners and
 * editors of the item's account may do so.
 */
class AddItemPhoto
{
    /**
     * The mime types we accept. Anything else is rejected, whatever the
     * extension of the uploaded file claims.
     */
    private const array ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    private const int MAX_SIZE_IN_BYTES = 10 * 1024 * 1024;

    private ItemPhoto $itemPhoto;

    private string $path;

    public function __construct(
        private readonly User $user,
        private readonly Item $item,
        private readonly UploadedFile $file,
    ) {}

    public function execute(): ItemPhoto
    {
        $this->validate();
        $this->store();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->itemPhoto;
    }

    private function validate(): void
    {
        if (! $this->item->collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! in_array($this->file->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidArgumentException('The file must be a jpeg, png, webp or gif image');
        }

        if ($this->file->getSize() > self::MAX_SIZE_IN_BYTES) {
            throw new InvalidArgumentException('The file must not be larger than 10 MB');
        }
    }

    /**
     * The name the user gave the file never reaches the disk: we generate a
     * random one instead, and keep the original in the database.
     */
    private function store(): void
    {
        $name = Str::uuid()->toString().'.'.$this->file->extension();

        $this->path = (string) $this->disk()->putFileAs('items/'.$this->item->id, $this->file, $name);
    }

    private function create(): void
    {
        $this->itemPhoto = ItemPhoto::query()->create([
            'item_id' => $this->item->id,
            'path' => $this->path,
            'filename' => $this->file->getClientOriginalName(),
            'mime_type' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'is_main' => $this->isFirstPhoto(),
            'position' => $this->nextPosition(),
        ]);
    }

    /**
     * An item always has exactly one main visual, so the first photo added to
     * it takes the role.
     */
    private function isFirstPhoto(): bool
    {
        return ! $this->item->photos()->exists();
    }

    /**
     * A position orders the photo within its item, so only the photos of that
     * item are considered.
     */
    private function nextPosition(): int
    {
        return (int) $this->item->photos()->max('position') + 1;
    }

    private function stampAuthor(): void
    {
        $this->itemPhoto->created_by_id = $this->user->id;
        $this->itemPhoto->created_by_name = $this->user->getFullName();
        $this->itemPhoto->updated_by_id = $this->user->id;
        $this->itemPhoto->updated_by_name = $this->user->getFullName();
        $this->itemPhoto->save();
    }

    /**
     * The disk lives here alone so it can be swapped in one place.
     */
    private function disk(): Filesystem
    {
        return Storage::disk((string) config('filesystems.default'));
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemPhotoCreation,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');
    }
}
