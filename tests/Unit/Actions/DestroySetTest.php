<?php

declare(strict_types=1);
use App\Actions\DestroySet;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a set', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $set = Set::factory()->forAccount($account->id)->create();

    new DestroySet(
        user: $owner,
        set: $set,
    )->execute();

    $this->assertSoftDeleted('sets', ['id' => $set->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SetDeletion,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $set = Set::factory()->forAccount($account->id)->create();

    new DestroySet(
        user: $viewer,
        set: $set,
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $set = Set::factory()->create();
    $stranger = $this->createUser();

    new DestroySet(
        user: $stranger,
        set: $set,
    )->execute();
});
