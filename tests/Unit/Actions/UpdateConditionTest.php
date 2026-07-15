<?php

declare(strict_types=1);
use App\Actions\UpdateCondition;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Condition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a condition and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $condition = Condition::factory()->create(['account_id' => $account->id, 'name' => 'Old name']);

    $result = new UpdateCondition(
        user: $editor,
        condition: $condition,
        name: 'Used',
    )->execute();

    expect($result)->toBeInstanceOf(Condition::class);
    expect($condition->fresh()->name)->toBe('Used');
    expect($condition->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ConditionUpdate,
    );
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $condition = Condition::factory()->create(['account_id' => $account->id]);

    new UpdateCondition(
        user: $owner,
        condition: $condition,
        name: '<strong>Used</strong>',
    )->execute();

    expect($condition->fresh()->name)->toBe('Used');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $condition = Condition::factory()->create(['account_id' => $account->id]);

    new UpdateCondition(
        user: $viewer,
        condition: $condition,
        name: 'Used',
    )->execute();
});

it('throws when the condition is a system default', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $owner = $this->createUser();
    $condition = Condition::factory()->systemDefault()->create();

    new UpdateCondition(
        user: $owner,
        condition: $condition,
        name: 'Used',
    )->execute();
});
