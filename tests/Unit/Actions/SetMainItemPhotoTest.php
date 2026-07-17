<?php

declare(strict_types=1);
use App\Actions\SetMainItemPhoto;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();

    $this->account = $this->createAccount();
    $this->editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $this->editor, account: $this->account, role: PermissionEnum::Editor->value);
    $this->collection = Collection::factory()->create(['account_id' => $this->account->id]);
    $this->item = Item::factory()->create(['collection_id' => $this->collection->id, 'name' => 'Amazing Spider-Man #1']);

    $this->rachel = ItemPhoto::factory()->create(['item_id' => $this->item->id, 'filename' => 'rachel.jpg', 'position' => 1, 'is_main' => true]);
    $this->monica = ItemPhoto::factory()->create(['item_id' => $this->item->id, 'filename' => 'monica.jpg', 'position' => 2, 'is_main' => false]);
});

it('sets a photo as the main one and unsets the previous one', function () {
    $photo = new SetMainItemPhoto(user: $this->editor, itemPhoto: $this->monica)->execute();

    expect($photo)->toBeInstanceOf(ItemPhoto::class);
    expect($this->monica->fresh()->is_main)->toBeTrue();
    expect($this->rachel->fresh()->is_main)->toBeFalse();
    expect($this->item->photos()->where('is_main', true)->count())->toBe(1);
    expect($this->item->fresh()->mainPhoto->id)->toBe($this->monica->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemPhotoUpdate,
    );
});

it('keeps the photo as the main one when it already is', function () {
    new SetMainItemPhoto(user: $this->editor, itemPhoto: $this->rachel)->execute();

    expect($this->rachel->fresh()->is_main)->toBeTrue();
    expect($this->item->photos()->where('is_main', true)->count())->toBe(1);
});

it('does not change the position of the photos', function () {
    new SetMainItemPhoto(user: $this->editor, itemPhoto: $this->monica)->execute();

    expect($this->rachel->fresh()->position)->toBe(1);
    expect($this->monica->fresh()->position)->toBe(2);
});

it('does not touch the main photo of another item', function () {
    $otherItem = Item::factory()->create(['collection_id' => $this->collection->id]);
    $otherItemMain = ItemPhoto::factory()->create(['item_id' => $otherItem->id, 'position' => 1, 'is_main' => true]);

    new SetMainItemPhoto(user: $this->editor, itemPhoto: $this->monica)->execute();

    expect($otherItemMain->fresh()->is_main)->toBeTrue();
});

it('throws when the user is only a viewer', function () {
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $this->account, role: PermissionEnum::Viewer->value);

    expect(fn () => new SetMainItemPhoto(user: $viewer, itemPhoto: $this->monica)->execute())
        ->toThrow(ModelNotFoundException::class);

    expect($this->rachel->fresh()->is_main)->toBeTrue();
    expect($this->monica->fresh()->is_main)->toBeFalse();
});

it('throws when the user does not belong to the account', function () {
    $stranger = $this->createUser();

    expect(fn () => new SetMainItemPhoto(user: $stranger, itemPhoto: $this->monica)->execute())
        ->toThrow(ModelNotFoundException::class);

    expect($this->rachel->fresh()->is_main)->toBeTrue();
    expect($this->monica->fresh()->is_main)->toBeFalse();
});
