<?php

declare(strict_types=1);
use App\Actions\DetachTagFromItem;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('takes a tag off an item and keeps the tag on the account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $removed = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);
    $kept = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Key issue']);
    $item->tags()->sync([$removed->id, $kept->id]);

    new DetachTagFromItem(user: $editor, item: $item, tag: $removed)->execute();

    expect($item->tags()->pluck('tags.id')->all())->toBe([$kept->id]);

    // Detaching unlinks, it does not delete the tag.
    $this->assertDatabaseHas('tags', ['id' => $removed->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemTagDetached,
    );
});

it('does nothing when the tag is not on the item', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new DetachTagFromItem(user: $owner, item: $item, tag: $tag)->execute();

    expect($item->tags()->count())->toBe(0);
});

it('throws when the tag belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignTag = Tag::factory()->create();

    new DetachTagFromItem(user: $owner, item: $item, tag: $foreignTag)->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id]);
    $item->tags()->sync([$tag->id]);

    new DetachTagFromItem(user: $viewer, item: $item, tag: $tag)->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new DetachTagFromItem(user: $stranger, item: $item, tag: $tag)->execute();
});
