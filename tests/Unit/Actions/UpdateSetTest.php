<?php

declare(strict_types=1);
use App\Actions\UpdateSet;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a set name and description', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $set = Set::factory()->forAccount($account->id)->create(['name' => 'Old name']);

    $set = new UpdateSet(
        user: $editor,
        set: $set,
        name: 'New name',
        description: 'New description',
    )->execute();

    expect($set->name)->toBe('New name');
    expect($set->description)->toBe('New description');
    expect($set->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SetUpdate,
    );
});

it('clears the description when null is given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $set = Set::factory()->forAccount($account->id)->create(['description' => 'Something']);

    $set = new UpdateSet(
        user: $owner,
        set: $set,
        name: 'Kept name',
    )->execute();

    expect($set->description)->toBeNull();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $set = Set::factory()->forAccount($account->id)->create();

    new UpdateSet(
        user: $viewer,
        set: $set,
        name: 'New name',
    )->execute();
});
