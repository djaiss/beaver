<?php

declare(strict_types=1);
use App\Actions\IndexItemPhotoSearchTokens;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

/**
 * A photo of the given user's account, indexed so it can be searched, with its
 * file actually written to the fake disk so deleting it is exercised too.
 */
function accountPhoto(User $user, string $filename, string $itemName, bool $isCover = false, int $size = 1000): ItemPhoto
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => $itemName]);
    $path = "items/{$item->id}/".Str::uuid()->toString().'.jpg';
    Storage::disk(config('filesystems.default'))->put($path, 'binary-image-bytes');

    $photo = ItemPhoto::factory()->create([
        'item_id' => $item->id,
        'path' => $path,
        'filename' => $filename,
        'is_main' => $isCover,
        'size' => $size,
    ]);

    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    return $photo;
}

it('lists the photos of the account', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'central_perk.jpg', 'The Coffee House');

    $this->actingAs($user)->get('/settings/photos')
        ->assertOk()
        ->assertSee('central_perk.jpg')
        ->assertSee('The Coffee House');
});

it('does not list the photos of another account', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $stranger = $this->createUser();
    accountPhoto($stranger, 'not_yours.jpg', 'Someone Elses Item');

    $this->actingAs($user)->get('/settings/photos')
        ->assertOk()
        ->assertDontSee('not_yours.jpg');
});

it('finds a photo by part of its file name', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'central_perk.jpg', 'The Coffee House');
    accountPhoto($user, 'smelly_cat.jpg', 'Phoebes Guitar');

    $this->actingAs($user)->get('/settings/photos?q=perk')
        ->assertOk()
        ->assertSee('central_perk.jpg')
        ->assertDontSee('smelly_cat.jpg');
});

it('finds a photo by part of the name of its item', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'img_0001.jpg', 'Phoebes Guitar');
    accountPhoto($user, 'img_0002.jpg', 'The Coffee House');

    $this->actingAs($user)->get('/settings/photos?q=guit')
        ->assertOk()
        ->assertSee('img_0001.jpg')
        ->assertDontSee('img_0002.jpg');
});

it('requires every word of the search to match', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'central_perk.jpg', 'The Coffee House');

    $this->actingAs($user)->get('/settings/photos?q=perk+guitar')
        ->assertOk()
        ->assertDontSee('central_perk.jpg');
});

it('finds nothing rather than everything when the search matches no photo', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'central_perk.jpg', 'The Coffee House');

    $this->actingAs($user)->get('/settings/photos?q=gunther')
        ->assertOk()
        ->assertDontSee('central_perk.jpg');
});

it('filters down to the covers', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'the_cover.jpg', 'Phoebes Guitar', isCover: true);
    accountPhoto($user, 'an_extra.jpg', 'Rosss Couch');

    $this->actingAs($user)->get('/settings/photos?filter=covers')
        ->assertOk()
        ->assertSee('the_cover.jpg')
        ->assertDontSee('an_extra.jpg');
});

it('filters down to the extras', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'the_cover.jpg', 'Phoebes Guitar', isCover: true);
    accountPhoto($user, 'an_extra.jpg', 'Rosss Couch');

    $this->actingAs($user)->get('/settings/photos?filter=extras')
        ->assertOk()
        ->assertSee('an_extra.jpg')
        ->assertDontSee('the_cover.jpg');
});

it('rejects a filter that does not exist', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->from('/settings/photos')
        ->get('/settings/photos?filter=blurry')
        ->assertSessionHasErrors('filter');
});

it('rejects a sort that does not exist', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->from('/settings/photos')
        ->get('/settings/photos?sort=prettiest')
        ->assertSessionHasErrors('sort');
});

it('paginates at a hundred photos a page', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    ItemPhoto::factory()->count(101)->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->get('/settings/photos')->assertOk();

    expect($response->viewData('photos')->perPage())->toBe(100);
    expect($response->viewData('photos')->total())->toBe(101);
    expect($response->viewData('photos')->lastPage())->toBe(2);
});

it('counts the whole account in the stats, not just the page', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    accountPhoto($user, 'one.jpg', 'Phoebes Guitar', isCover: true, size: 1024);
    accountPhoto($user, 'two.jpg', 'Rosss Couch', size: 1024);

    $stats = $this->actingAs($user)->get('/settings/photos')->viewData('stats');

    expect($stats['total'])->toBe(2);
    expect($stats['covers'])->toBe(1);
    expect($stats['items'])->toBe(2);
    expect($stats['storage'])->toBe('2 KB');
});

it('deletes a photo', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $photo = accountPhoto($user, 'central_perk.jpg', 'The Coffee House');

    $this->actingAs($user)
        ->delete("/settings/photos/{$photo->id}")
        ->assertRedirect('/settings/photos');

    $this->assertModelMissing($photo);
    Storage::disk(config('filesystems.default'))->assertMissing($photo->path);
});

it('does not delete a photo of another account', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $photo = accountPhoto($this->createUser(), 'not_yours.jpg', 'Someone Elses Item');

    $this->actingAs($user)->delete("/settings/photos/{$photo->id}")->assertNotFound();

    $this->assertModelExists($photo);
});

it('keeps a viewer out', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->get('/settings/photos')->assertNotFound();
});

it('requires authentication', function () {
    $this->get('/settings/photos')->assertRedirect('/login');
});
