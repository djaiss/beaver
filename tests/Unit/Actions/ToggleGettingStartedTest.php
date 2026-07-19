<?php

declare(strict_types=1);
use App\Actions\ToggleGettingStarted;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('hides the getting started screen and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $account = new ToggleGettingStarted(
        user: $owner,
        account: $account,
        show: false,
    )->execute();

    expect($account)->toBeInstanceOf(Account::class);
    expect($account->show_getting_started)->toBeFalse();
    expect($account->updated_by_name)->toBe('Ross Geller');

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'show_getting_started' => false,
        'updated_by_id' => $owner->id,
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::GettingStartedUpdate,
    );
});

it('brings the getting started screen back', function () {
    Queue::fake();

    $account = $this->createAccount();
    $account->update(['show_getting_started' => false]);
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $account = new ToggleGettingStarted(
        user: $owner,
        account: $account,
        show: true,
    )->execute();

    expect($account->show_getting_started)->toBeTrue();
});

it('refuses an editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    new ToggleGettingStarted(
        user: $editor,
        account: $account,
        show: false,
    )->execute();
})->throws(ModelNotFoundException::class);

it('refuses a viewer', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new ToggleGettingStarted(
        user: $viewer,
        account: $account,
        show: false,
    )->execute();
})->throws(ModelNotFoundException::class);

it('refuses an owner of another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $other = $this->createAccount('Other');
    $stranger = $this->createUser();
    $this->assignUserToAccount(user: $stranger, account: $other, role: PermissionEnum::Owner->value);

    new ToggleGettingStarted(
        user: $stranger,
        account: $account,
        show: false,
    )->execute();
})->throws(ModelNotFoundException::class);
