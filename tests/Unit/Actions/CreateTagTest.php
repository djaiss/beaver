<?php

declare(strict_types=1);
use App\Actions\CreateTag;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a tag and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $tag = new CreateTag(
        user: $editor,
        account: $account,
        name: 'Signed',
    )->execute();

    expect($tag)->toBeInstanceOf(Tag::class);
    expect($tag->name)->toBe('Signed');
    expect($tag->account_id)->toBe($account->id);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($tag->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TagCreation,
    );
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $tag = new CreateTag(
        user: $owner,
        account: $account,
        name: '<strong>Signed</strong>',
    )->execute();

    expect($tag->name)->toBe('Signed');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateTag(
        user: $viewer,
        account: $account,
        name: 'Signed',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateTag(
        user: $stranger,
        account: $account,
        name: 'Signed',
    )->execute();
});
