<?php

declare(strict_types=1);
use App\Actions\AddItemPhoto;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

it('adds a photo, stores the file and stamps the author', function () {
    $photo = new AddItemPhoto(
        user: $this->editor,
        item: $this->item,
        file: UploadedFile::fake()->image('central-perk.jpg'),
    )->execute();

    expect($photo)->toBeInstanceOf(ItemPhoto::class);
    expect($photo->filename)->toBe('central-perk.jpg');
    expect($photo->mime_type)->toBe('image/jpeg');
    expect($photo->item_id)->toBe($this->item->id);
    expect($photo->size)->toBeGreaterThan(0);

    Storage::assertExists($photo->path);
    expect($photo->path)->toStartWith('items/'.$this->item->id.'/');
    expect($photo->path)->not->toContain('central-perk');

    $this->assertDatabaseHas('item_photos', [
        'id' => $photo->id,
        'item_id' => $this->item->id,
        'created_by_id' => $this->editor->id,
        'updated_by_id' => $this->editor->id,
    ]);
    expect($photo->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemPhotoCreation,
    );
});

it('keeps the original filename encrypted at rest', function () {
    $photo = new AddItemPhoto(
        user: $this->editor,
        item: $this->item,
        file: UploadedFile::fake()->image('central-perk.jpg'),
    )->execute();

    $rawFilename = DB::table('item_photos')->where('id', $photo->id)->value('filename');

    expect(decrypt($rawFilename, false))->toBe('central-perk.jpg');
});

it('makes the first photo of an item the main one', function () {
    $first = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();

    expect($first->is_main)->toBeTrue();
});

it('does not make later photos the main one', function () {
    $first = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();

    expect($first->fresh()->is_main)->toBeTrue();
    expect($second->is_main)->toBeFalse();
    expect($this->item->photos()->where('is_main', true)->count())->toBe(1);
});

it('increments the position for each photo of the item', function () {
    $first = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();
    $third = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('phoebe.jpg'))->execute();

    expect($first->position)->toBe(1);
    expect($second->position)->toBe(2);
    expect($third->position)->toBe(3);
});

it('scopes the position to the item, not to all the items', function () {
    $otherItem = Item::factory()->create(['collection_id' => $this->collection->id, 'name' => 'The Incredible Hulk #1']);

    new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();
    $photo = new AddItemPhoto(user: $this->editor, item: $otherItem, file: UploadedFile::fake()->image('joey.jpg'))->execute();

    expect($photo->position)->toBe(1);
    expect($photo->is_main)->toBeTrue();
});

it('stores each photo under its own generated name', function () {
    $first = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();

    expect($first->path)->not->toBe($second->path);
    Storage::assertExists($first->path);
    Storage::assertExists($second->path);
});

it('rejects a file that is not an image', function () {
    expect(fn () => new AddItemPhoto(
        user: $this->editor,
        item: $this->item,
        file: UploadedFile::fake()->create('the-one-with-the-list.pdf', 100, 'application/pdf'),
    )->execute())->toThrow(InvalidArgumentException::class);

    expect(ItemPhoto::query()->count())->toBe(0);
});

it('rejects an image larger than 10 MB', function () {
    expect(fn () => new AddItemPhoto(
        user: $this->editor,
        item: $this->item,
        file: UploadedFile::fake()->image('gunther.jpg')->size(11 * 1024),
    )->execute())->toThrow(InvalidArgumentException::class);

    expect(ItemPhoto::query()->count())->toBe(0);
});

it('throws when the user is only a viewer', function () {
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $this->account, role: PermissionEnum::Viewer->value);

    expect(fn () => new AddItemPhoto(
        user: $viewer,
        item: $this->item,
        file: UploadedFile::fake()->image('rachel.jpg'),
    )->execute())->toThrow(ModelNotFoundException::class);

    expect(ItemPhoto::query()->count())->toBe(0);
});

it('throws when the user does not belong to the account', function () {
    $stranger = $this->createUser();

    expect(fn () => new AddItemPhoto(
        user: $stranger,
        item: $this->item,
        file: UploadedFile::fake()->image('rachel.jpg'),
    )->execute())->toThrow(ModelNotFoundException::class);

    expect(ItemPhoto::query()->count())->toBe(0);
});
