<?php

declare(strict_types=1);
use App\Actions\RemoveAccountMember;
use App\Enums\PermissionEnum;
use App\Jobs\LogUserAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('removes a member from an account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $member = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $account,
        role: PermissionEnum::Viewer->value,
    );

    new RemoveAccountMember(
        user: $owner,
        account: $account,
        member: $member,
    )->execute();

    $this->assertModelMissing($member);

    Queue::assertPushedOn(queue: 'low', job: LogUserAction::class);
});

it('throws when the user is not an owner', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $member = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $account,
        role: PermissionEnum::Viewer->value,
    );

    new RemoveAccountMember(
        user: $viewer,
        account: $account,
        member: $member,
    )->execute();
});

it('throws when the member belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $otherAccount = $this->createAccount();
    $foreignMember = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $otherAccount,
        role: PermissionEnum::Viewer->value,
    );

    new RemoveAccountMember(
        user: $owner,
        account: $account,
        member: $foreignMember,
    )->execute();
});

it('throws when removing the last owner', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new RemoveAccountMember(
        user: $owner,
        account: $account,
        member: $member,
    )->execute();
});
