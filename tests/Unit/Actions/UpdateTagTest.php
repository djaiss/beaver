<?php

declare(strict_types=1);
use App\Actions\UpdateTag;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a tag and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $tag = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Old name']);

    $result = new UpdateTag(
        user: $editor,
        tag: $tag,
        name: 'Signed',
    )->execute();

    expect($result)->toBeInstanceOf(Tag::class);
    expect($tag->fresh()->name)->toBe('Signed');
    expect($tag->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TagUpdate,
    );
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new UpdateTag(
        user: $owner,
        tag: $tag,
        name: '<strong>Signed</strong>',
    )->execute();

    expect($tag->fresh()->name)->toBe('Signed');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new UpdateTag(
        user: $viewer,
        tag: $tag,
        name: 'Signed',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new UpdateTag(
        user: $stranger,
        tag: $tag,
        name: 'Signed',
    )->execute();
});
