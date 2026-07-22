<?php

declare(strict_types=1);
use App\Actions\EmptyTrash;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('permanently deletes everything in the trash', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collection = Collection::factory()->create(['account_id' => $account->id, 'name' => 'Vintage Vinyl']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $set = Set::factory()->forAccount($account->id)->create();

    $copy->delete();
    $item->delete();
    $collection->delete();
    $set->delete();

    $deleted = new EmptyTrash(
        user: $owner,
        account: $account,
    )->execute();

    expect($deleted)->toBe(4);
    $this->assertDatabaseMissing('copies', ['id' => $copy->id]);
    $this->assertDatabaseMissing('items', ['id' => $item->id]);
    $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    $this->assertDatabaseMissing('sets', ['id' => $set->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TrashEmptied,
    );
});

it('leaves objects that are not in the trash alone', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $kept = Collection::factory()->create(['account_id' => $account->id]);
    $trashed = Collection::factory()->create(['account_id' => $account->id]);
    $trashed->delete();

    $deleted = new EmptyTrash(
        user: $editor,
        account: $account,
    )->execute();

    expect($deleted)->toBe(1);
    $this->assertDatabaseHas('collections', ['id' => $kept->id]);
    $this->assertDatabaseMissing('collections', ['id' => $trashed->id]);
});

it('leaves another account trash alone', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $otherAccount = $this->createAccount('Moondance Diner');
    $otherCollection = Collection::factory()->create(['account_id' => $otherAccount->id]);
    $otherCollection->delete();

    $deleted = new EmptyTrash(
        user: $owner,
        account: $account,
    )->execute();

    expect($deleted)->toBe(0);
    $this->assertDatabaseHas('collections', ['id' => $otherCollection->id]);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new EmptyTrash(
        user: $viewer,
        account: $account,
    )->execute();
});
