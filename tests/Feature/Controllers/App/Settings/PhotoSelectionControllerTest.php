<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function selectablePhoto(User $user): ItemPhoto
{
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $path = "items/{$item->id}/".Str::uuid()->toString().'.jpg';
    Storage::disk(config('filesystems.default'))->put($path, 'binary-image-bytes');

    return ItemPhoto::factory()->create(['item_id' => $item->id, 'path' => $path]);
}

it('deletes every photo of the selection', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $first = selectablePhoto($user);
    $second = selectablePhoto($user);

    $this->actingAs($user)
        ->delete('/settings/photos/selection', ['ids' => [$first->id, $second->id]])
        ->assertRedirect('/settings/photos');

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
    Storage::disk(config('filesystems.default'))->assertMissing($first->path);
});

it('deletes nothing when the selection reaches into another account', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $mine = selectablePhoto($user);
    $theirs = selectablePhoto($this->createUser());

    $this->actingAs($user)
        ->delete('/settings/photos/selection', ['ids' => [$mine->id, $theirs->id]])
        ->assertNotFound();

    $this->assertModelExists($mine);
    $this->assertModelExists($theirs);
});

it('requires at least one photo', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->from('/settings/photos')
        ->delete('/settings/photos/selection', ['ids' => []])
        ->assertSessionHasErrors('ids');
});

it('keeps a viewer out', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Viewer->value);
    $photo = selectablePhoto($viewer);

    $this->actingAs($viewer)
        ->delete('/settings/photos/selection', ['ids' => [$photo->id]])
        ->assertNotFound();

    $this->assertModelExists($photo);
});

it('requires authentication', function () {
    $this->delete('/settings/photos/selection', ['ids' => [1]])->assertRedirect('/login');
});
