<?php

declare(strict_types=1);
use App\Actions\AddItemPhoto;
use App\Actions\ResizeItemPhoto;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Storage::fake();

    $this->account = $this->createAccount();
    $this->editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $this->editor, account: $this->account, role: PermissionEnum::Editor->value);
    $this->collection = Collection::factory()->create(['account_id' => $this->account->id]);
    $this->item = Item::factory()->create(['collection_id' => $this->collection->id, 'name' => 'Amazing Spider-Man #1']);
});

/**
 * Store a real image of the given size as a photo of the item, so the resize
 * has an actual file to read from the fake disk.
 */
function storePhoto(int $width, int $height, string $name = 'central-perk.jpg')
{
    return new AddItemPhoto(
        user: test()->editor,
        item: test()->item,
        file: UploadedFile::fake()->image($name, $width, $height),
    )->execute();
}

/**
 * @return array{0: int, 1: int}
 */
function dimensionsOf(string $path): array
{
    $size = getimagesizefromstring(Storage::disk(config('filesystems.default'))->get($path));

    return [$size[0], $size[1]];
}

it('resizes the photo to fit the box while preserving the ratio', function () {
    $photo = storePhoto(800, 600);

    $path = new ResizeItemPhoto(
        user: $this->editor,
        itemPhoto: $photo,
        width: 400,
        height: 400,
    )->execute();

    // 800x600 fits a 400x400 box at 400x300, so the ratio is kept.
    expect(dimensionsOf($path))->toBe([400, 300]);
    Storage::assertExists($path);
});

it('stores the variant beside the original, named after the requested box', function () {
    $photo = storePhoto(800, 600);

    $path = new ResizeItemPhoto(
        user: $this->editor,
        itemPhoto: $photo,
        width: 400,
        height: 400,
    )->execute();

    expect($path)->toBe(substr($photo->path, 0, -4).'_400x400.jpg');
    expect($path)->toStartWith('items/'.$this->item->id.'/');

    // The original is left untouched at its full size.
    Storage::assertExists($photo->path);
    expect(dimensionsOf($photo->path))->toBe([800, 600]);
});

it('never enlarges a photo smaller than the box', function () {
    $photo = storePhoto(100, 100);

    $path = new ResizeItemPhoto(
        user: $this->editor,
        itemPhoto: $photo,
        width: 400,
        height: 400,
    )->execute();

    expect(dimensionsOf($path))->toBe([100, 100]);
});

it('keeps the format of the original', function () {
    $photo = storePhoto(800, 600, 'central-perk.png');

    $path = new ResizeItemPhoto(
        user: $this->editor,
        itemPhoto: $photo,
        width: 200,
        height: 200,
    )->execute();

    $mime = getimagesizefromstring(Storage::disk(config('filesystems.default'))->get($path))['mime'];
    expect($mime)->toBe('image/png');
});

it('overwrites the variant when the same box is requested again', function () {
    $photo = storePhoto(800, 600);

    $first = new ResizeItemPhoto(user: $this->editor, itemPhoto: $photo, width: 400, height: 400)->execute();
    $second = new ResizeItemPhoto(user: $this->editor, itemPhoto: $photo, width: 400, height: 400)->execute();

    expect($second)->toBe($first);
});

it('throws when the width is not positive', function () {
    $photo = storePhoto(800, 600);

    expect(fn () => new ResizeItemPhoto(user: $this->editor, itemPhoto: $photo, width: 0, height: 400)->execute())
        ->toThrow(InvalidArgumentException::class);
});

it('throws when the height is not positive', function () {
    $photo = storePhoto(800, 600);

    expect(fn () => new ResizeItemPhoto(user: $this->editor, itemPhoto: $photo, width: 400, height: -10)->execute())
        ->toThrow(InvalidArgumentException::class);
});

it('throws when the user is only a viewer', function () {
    $photo = storePhoto(800, 600);

    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $this->account, role: PermissionEnum::Viewer->value);

    expect(fn () => new ResizeItemPhoto(user: $viewer, itemPhoto: $photo, width: 400, height: 400)->execute())
        ->toThrow(ModelNotFoundException::class);
});

it('throws when the user belongs to another account', function () {
    $photo = storePhoto(800, 600);

    $stranger = $this->createUser();

    expect(fn () => new ResizeItemPhoto(user: $stranger, itemPhoto: $photo, width: 400, height: 400)->execute())
        ->toThrow(ModelNotFoundException::class);
});
