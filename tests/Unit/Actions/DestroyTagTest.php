<?php

declare(strict_types=1);
use App\Actions\DestroyTag;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a tag', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new DestroyTag(
        user: $owner,
        tag: $tag,
    )->execute();

    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TagDeletion,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new DestroyTag(
        user: $viewer,
        tag: $tag,
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $tag = Tag::factory()->create(['account_id' => $account->id]);

    new DestroyTag(
        user: $stranger,
        tag: $tag,
    )->execute();
});
