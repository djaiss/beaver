<?php

declare(strict_types=1);
use App\Actions\UpdateAccount;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('renames an account for an owner', function () {
    Queue::fake();

    $account = $this->createAccount(name: 'Old name');
    $owner = $this->createUser(['first_name' => 'Joey', 'last_name' => 'Tribbiani']);
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $result = new UpdateAccount(
        user: $owner,
        account: $account,
        name: 'Central Perk',
    )->execute();

    expect($result)->toBeInstanceOf(Account::class);
    expect($account->fresh()->name)->toBe('Central Perk');
    expect($account->fresh()->updated_by_id)->toBe($owner->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::AccountUpdate,
    );
});
it('throws when the user is not an owner', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new UpdateAccount(
        user: $viewer,
        account: $account,
        name: 'Central Perk',
    )->execute();
});
