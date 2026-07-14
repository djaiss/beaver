<?php

declare(strict_types=1);
use App\Actions\CreateCollection;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a collection and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $collection = new CreateCollection(
        user: $editor,
        account: $account,
        name: 'Marvel Comics 1990s',
        description: 'My run of 90s Marvel',
        emoji: '📚',
        visibility: VisibilityEnum::Shared->value,
        currency: 'USD',
    )->execute();

    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection->name)->toBe('Marvel Comics 1990s');
    expect($collection->description)->toBe('My run of 90s Marvel');
    expect($collection->emoji)->toBe('📚');
    expect($collection->visibility)->toBe(VisibilityEnum::Shared);
    expect($collection->account_id)->toBe($account->id);
    expect($collection->uuid)->not->toBeEmpty();

    $this->assertDatabaseHas('collections', [
        'id' => $collection->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($collection->created_by_name)->toBe('Ross Geller');
    expect($collection->updated_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CollectionCreation,
    );
});

it('sanitizes the name, description and emoji', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collection = new CreateCollection(
        user: $owner,
        account: $account,
        name: '<strong>Vinyl Records</strong>',
        description: '<em>Rare pressings</em>',
        emoji: '<script>alert(1)</script>💿',
    )->execute();

    expect($collection->name)->toBe('Vinyl Records');
    expect($collection->description)->toBe('Rare pressings');
    expect($collection->emoji)->toBe('💿');
});

it('defaults the visibility to private', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collection = new CreateCollection(
        user: $owner,
        account: $account,
        name: 'Wine Cellar',
    )->execute();

    expect($collection->visibility)->toBe(VisibilityEnum::Private);
});

it('throws when the visibility is invalid', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new CreateCollection(
        user: $owner,
        account: $account,
        name: 'Wine Cellar',
        visibility: 'secret',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateCollection(
        user: $viewer,
        account: $account,
        name: 'Wine Cellar',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateCollection(
        user: $stranger,
        account: $account,
        name: 'Wine Cellar',
    )->execute();
});
