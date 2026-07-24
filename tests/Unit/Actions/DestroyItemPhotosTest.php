<?php

declare(strict_types=1);
use App\Actions\DestroyItemPhotos;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function storedPhoto(Item $item, bool $isMain = false, int $position = 1): ItemPhoto
{
    $path = "items/{$item->id}/".Str::uuid()->toString().'.jpg';
    Storage::disk(config('filesystems.default'))->put($path, 'binary-image-bytes');

    return ItemPhoto::factory()->create([
        'item_id' => $item->id,
        'path' => $path,
        'is_main' => $isMain,
        'position' => $position,
    ]);
}

it('deletes every photo it is given', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $first = storedPhoto($item, isMain: true, position: 1);
    $second = storedPhoto($item, position: 2);

    $deleted = new DestroyItemPhotos(
        user: $user,
        account: $user->account,
        photoIds: [$first->id, $second->id],
    )->execute();

    expect($deleted)->toBe(2);
    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
});

it('removes the files from the disk too', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $photo = storedPhoto($item);

    new DestroyItemPhotos(user: $user, account: $user->account, photoIds: [$photo->id])->execute();

    Storage::disk(config('filesystems.default'))->assertMissing($photo->path);
});

it('promotes the next photo when the cover is among those deleted', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $cover = storedPhoto($item, isMain: true, position: 1);
    $other = storedPhoto($item, position: 2);

    new DestroyItemPhotos(user: $user, account: $user->account, photoIds: [$cover->id])->execute();

    expect($other->fresh()->is_main)->toBeTrue();
});

it('refuses to delete a photo of another account', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $foreign = Item::factory()->create();
    $photo = storedPhoto($foreign);

    expect(fn () => new DestroyItemPhotos(
        user: $user,
        account: $user->account,
        photoIds: [$photo->id],
    )->execute())->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($photo);
});

it('refuses a viewer', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $photo = storedPhoto($item);

    expect(fn () => new DestroyItemPhotos(
        user: $viewer,
        account: $account,
        photoIds: [$photo->id],
    )->execute())->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($photo);
});

it('deletes nothing at all when one of the photos is not the accounts', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $mine = storedPhoto(Item::factory()->create(['catalog_id' => $catalog->id]));
    $foreign = storedPhoto(Item::factory()->create());

    expect(fn () => new DestroyItemPhotos(
        user: $user,
        account: $user->account,
        photoIds: [$mine->id, $foreign->id],
    )->execute())->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($mine);
    $this->assertModelExists($foreign);
});
