<?php

declare(strict_types=1);
use App\Actions\MoveItemPhoto;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
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
    $this->catalog = Catalog::factory()->create(['account_id' => $this->account->id]);
    $this->item = Item::factory()->create(['catalog_id' => $this->catalog->id, 'name' => 'Amazing Spider-Man #1']);

    $this->rachel = ItemPhoto::factory()->create(['item_id' => $this->item->id, 'filename' => 'rachel.jpg', 'position' => 1, 'is_main' => true]);
    $this->monica = ItemPhoto::factory()->create(['item_id' => $this->item->id, 'filename' => 'monica.jpg', 'position' => 2]);
    $this->phoebe = ItemPhoto::factory()->create(['item_id' => $this->item->id, 'filename' => 'phoebe.jpg', 'position' => 3]);
});

it('moves a photo up', function () {
    new MoveItemPhoto(user: $this->editor, itemPhoto: $this->monica, direction: 'up')->execute();

    expect($this->monica->fresh()->position)->toBe(1);
    expect($this->rachel->fresh()->position)->toBe(2);
    expect($this->item->photos()->pluck('id')->all())->toBe([$this->monica->id, $this->rachel->id, $this->phoebe->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemPhotoUpdate,
    );
});

it('moves a photo down', function () {
    new MoveItemPhoto(user: $this->editor, itemPhoto: $this->monica, direction: 'down')->execute();

    expect($this->monica->fresh()->position)->toBe(3);
    expect($this->phoebe->fresh()->position)->toBe(2);
    expect($this->item->photos()->pluck('id')->all())->toBe([$this->rachel->id, $this->phoebe->id, $this->monica->id]);
});

it('does nothing when moving the first photo up', function () {
    new MoveItemPhoto(user: $this->editor, itemPhoto: $this->rachel, direction: 'up')->execute();

    expect($this->rachel->fresh()->position)->toBe(1);
    expect($this->monica->fresh()->position)->toBe(2);
    expect($this->phoebe->fresh()->position)->toBe(3);
});

it('does nothing when moving the last photo down', function () {
    new MoveItemPhoto(user: $this->editor, itemPhoto: $this->phoebe, direction: 'down')->execute();

    expect($this->rachel->fresh()->position)->toBe(1);
    expect($this->monica->fresh()->position)->toBe(2);
    expect($this->phoebe->fresh()->position)->toBe(3);
});

it('does not change which photo is the main one', function () {
    new MoveItemPhoto(user: $this->editor, itemPhoto: $this->monica, direction: 'up')->execute();

    expect($this->rachel->fresh()->is_main)->toBeTrue();
    expect($this->monica->fresh()->is_main)->toBeFalse();
});

it('only considers the photos of the same item', function () {
    $otherItem = Item::factory()->create(['catalog_id' => $this->catalog->id]);
    $otherPhoto = ItemPhoto::factory()->create(['item_id' => $otherItem->id, 'position' => 1]);

    new MoveItemPhoto(user: $this->editor, itemPhoto: $this->rachel, direction: 'up')->execute();

    expect($otherPhoto->fresh()->position)->toBe(1);
    expect($this->rachel->fresh()->position)->toBe(1);
});

it('throws when the user is only a viewer', function () {
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $this->account, role: PermissionEnum::Viewer->value);

    expect(fn () => new MoveItemPhoto(user: $viewer, itemPhoto: $this->monica, direction: 'up')->execute())
        ->toThrow(ModelNotFoundException::class);

    expect($this->monica->fresh()->position)->toBe(2);
});

it('throws when the user does not belong to the account', function () {
    $stranger = $this->createUser();

    expect(fn () => new MoveItemPhoto(user: $stranger, itemPhoto: $this->monica, direction: 'up')->execute())
        ->toThrow(ModelNotFoundException::class);

    expect($this->monica->fresh()->position)->toBe(2);
});
