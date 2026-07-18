<?php

declare(strict_types=1);
use App\Actions\AttachTagToItem;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('puts an existing tag on an item', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man #1']);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $attached = new AttachTagToItem(user: $editor, item: $item, name: 'Signed')->execute();

    expect($attached)->toBeInstanceOf(Tag::class);
    expect($attached->id)->toBe($tag->id);
    expect($item->tags()->pluck('tags.id')->all())->toBe([$tag->id]);

    // No second tag reading the same as the one the account already had.
    expect(Tag::query()->count())->toBe(1);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemTagAttached,
    );
});

// A tag name is encrypted, so the match happens in PHP. Getting the case wrong
// must still find the tag rather than quietly creating a lookalike.
it('matches an existing tag whatever the case', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Key Issue']);

    $attached = new AttachTagToItem(user: $owner, item: $item, name: 'key issue')->execute();

    expect($attached->id)->toBe($tag->id);
    expect(Tag::query()->count())->toBe(1);
});

it('creates the tag when the account does not have it yet', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $attached = new AttachTagToItem(user: $owner, item: $item, name: 'First print')->execute();

    expect($attached->name)->toBe('First print');
    expect($attached->account_id)->toBe($account->id);
    expect($attached->created_by_name)->toBe('Rachel Green');
    expect($item->tags()->pluck('tags.id')->all())->toBe([$attached->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TagCreation,
    );
});

// The pivot refuses a duplicate, so tagging twice has to be a no-op rather than
// a crash. A double click is the obvious way to get here.
it('leaves the item alone when the tag is already on it', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);
    $item->tags()->sync([$tag->id]);

    new AttachTagToItem(user: $owner, item: $item, name: 'Signed')->execute();

    expect($item->tags()->count())->toBe(1);
    expect(Tag::query()->count())->toBe(1);

    // Nothing changed, so the activity feed must not claim the tag was added.
    Queue::assertNotPushed(
        LogUserAction::class,
        fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemTagAttached,
    );
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $attached = new AttachTagToItem(user: $owner, item: $item, name: '<strong>Signed</strong>')->execute();

    expect($attached->name)->toBe('Signed');
});

it('throws when the name is blank', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new AttachTagToItem(user: $owner, item: $item, name: '   ')->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new AttachTagToItem(user: $viewer, item: $item, name: 'Signed')->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new AttachTagToItem(user: $stranger, item: $item, name: 'Signed')->execute();
});
