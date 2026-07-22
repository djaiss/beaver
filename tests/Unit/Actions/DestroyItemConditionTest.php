<?php

declare(strict_types=1);
use App\Actions\DestroyItemCondition;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\ItemCondition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a condition', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);

    new DestroyItemCondition(
        user: $owner,
        itemCondition: $condition,
    )->execute();

    $this->assertDatabaseMissing('item_conditions', ['id' => $condition->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ConditionDeletion,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);

    new DestroyItemCondition(
        user: $viewer,
        itemCondition: $condition,
    )->execute();
});

it('throws when the condition is a system default', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $owner = $this->createUser();
    $condition = ItemCondition::factory()->systemDefault()->create();

    new DestroyItemCondition(
        user: $owner,
        itemCondition: $condition,
    )->execute();
});
