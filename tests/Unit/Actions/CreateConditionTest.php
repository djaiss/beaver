<?php

declare(strict_types=1);
use App\Actions\CreateCondition;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Condition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a condition and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $condition = new CreateCondition(
        user: $editor,
        account: $account,
        name: 'New',
    )->execute();

    expect($condition)->toBeInstanceOf(Condition::class);
    expect($condition->name)->toBe('New');
    expect($condition->account_id)->toBe($account->id);

    $this->assertDatabaseHas('conditions', [
        'id' => $condition->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($condition->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ConditionCreation,
    );
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $condition = new CreateCondition(
        user: $owner,
        account: $account,
        name: '<strong>New</strong>',
    )->execute();

    expect($condition->name)->toBe('New');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateCondition(
        user: $viewer,
        account: $account,
        name: 'New',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateCondition(
        user: $stranger,
        account: $account,
        name: 'New',
    )->execute();
});
