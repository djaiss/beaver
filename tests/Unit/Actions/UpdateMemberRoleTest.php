<?php

declare(strict_types=1);
use App\Actions\UpdateMemberRole;
use App\Enums\PermissionEnum;
use App\Jobs\LogUserAction;
use App\Models\AccountMember;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('updates the role of a member', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $member = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $account,
        role: PermissionEnum::Viewer->value,
    );

    $result = new UpdateMemberRole(
        user: $owner,
        account: $account,
        member: $member,
        role: PermissionEnum::Editor->value,
    )->execute();

    expect($result)->toBeInstanceOf(AccountMember::class);
    expect($member->fresh()->role)->toBe(PermissionEnum::Editor->value);

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

    new UpdateMemberRole(
        user: $viewer,
        account: $account,
        member: $member,
        role: PermissionEnum::Editor->value,
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

    new UpdateMemberRole(
        user: $owner,
        account: $account,
        member: $foreignMember,
        role: PermissionEnum::Editor->value,
    )->execute();
});
it('throws when the role is invalid', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $member = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $account,
        role: PermissionEnum::Viewer->value,
    );

    new UpdateMemberRole(
        user: $owner,
        account: $account,
        member: $member,
        role: 'superhero',
    )->execute();
});
it('throws when demoting the last owner', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new UpdateMemberRole(
        user: $owner,
        account: $account,
        member: $member,
        role: PermissionEnum::Viewer->value,
    )->execute();
});
