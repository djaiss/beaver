<?php

declare(strict_types=1);
use App\Actions\AddItemPhoto;
use App\Actions\DestroyItemPhoto;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\ItemPhoto;
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
    $this->catalog = Catalog::factory()->create(['account_id' => $this->account->id]);
    $this->item = Item::factory()->create(['catalog_id' => $this->catalog->id, 'name' => 'Amazing Spider-Man #1']);
});

it('deletes the photo and removes the file from the disk', function () {
    $photo = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();

    Storage::assertExists($photo->path);

    new DestroyItemPhoto(user: $this->editor, itemPhoto: $photo)->execute();

    $this->assertModelMissing($photo);
    Storage::assertMissing($photo->path);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemPhotoDeletion,
    );
});

it('promotes the next photo when the main one is deleted', function () {
    $main = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();
    $third = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('phoebe.jpg'))->execute();

    expect($main->is_main)->toBeTrue();

    new DestroyItemPhoto(user: $this->editor, itemPhoto: $main)->execute();

    expect($second->fresh()->is_main)->toBeTrue();
    expect($third->fresh()->is_main)->toBeFalse();
    expect($this->item->photos()->where('is_main', true)->count())->toBe(1);
});

it('promotes the remaining photo with the lowest position', function () {
    $main = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();
    $third = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('phoebe.jpg'))->execute();

    // Push the third photo ahead of the second one.
    $third->update(['position' => 0]);

    new DestroyItemPhoto(user: $this->editor, itemPhoto: $main)->execute();

    expect($third->fresh()->is_main)->toBeTrue();
    expect($second->fresh()->is_main)->toBeFalse();
});

it('does not promote anything when a photo that is not the main one is deleted', function () {
    $main = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();

    new DestroyItemPhoto(user: $this->editor, itemPhoto: $second)->execute();

    expect($main->fresh()->is_main)->toBeTrue();
    expect($this->item->photos()->count())->toBe(1);
});

it('leaves the item without a main photo when the last photo is deleted', function () {
    $only = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();

    new DestroyItemPhoto(user: $this->editor, itemPhoto: $only)->execute();

    expect($this->item->photos()->count())->toBe(0);
    expect($this->item->fresh()->mainPhoto)->toBeNull();
});

it('only removes the file of the deleted photo', function () {
    $first = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('rachel.jpg'))->execute();
    $second = new AddItemPhoto(user: $this->editor, item: $this->item, file: UploadedFile::fake()->image('monica.jpg'))->execute();

    new DestroyItemPhoto(user: $this->editor, itemPhoto: $second)->execute();

    Storage::assertMissing($second->path);
    Storage::assertExists($first->path);
});

it('throws when the user is only a viewer', function () {
    $photo = ItemPhoto::factory()->create(['item_id' => $this->item->id]);
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $this->account, role: PermissionEnum::Viewer->value);

    expect(fn () => new DestroyItemPhoto(user: $viewer, itemPhoto: $photo)->execute())
        ->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($photo);
});

it('throws when the user does not belong to the account', function () {
    $photo = ItemPhoto::factory()->create(['item_id' => $this->item->id]);
    $stranger = $this->createUser();

    expect(fn () => new DestroyItemPhoto(user: $stranger, itemPhoto: $photo)->execute())
        ->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($photo);
});
