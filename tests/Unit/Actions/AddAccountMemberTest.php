<?php

declare(strict_types=1);
use App\Actions\AddAccountMember;
use App\Enums\PermissionEnum;
use App\Models\AccountMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('adds a member to an account', function () {
    $account = $this->createAccount();
    $user = $this->createUser();
    $inviter = $this->createUser();

    $member = new AddAccountMember(
        account: $account,
        user: $user,
        role: PermissionEnum::Editor->value,
        invitedBy: $inviter,
    )->execute();

    expect($member)->toBeInstanceOf(AccountMember::class);
    expect($member->joined_at)->not->toBeNull();
    $this->assertDatabaseHas('account_user', [
        'id' => $member->id,
        'account_id' => $account->id,
        'user_id' => $user->id,
        'role' => PermissionEnum::Editor->value,
        'invited_by' => $inviter->id,
    ]);
});
it('defaults to the viewer role', function () {
    $account = $this->createAccount();
    $user = $this->createUser();

    $member = new AddAccountMember(
        account: $account,
        user: $user,
    )->execute();

    expect($member->role)->toBe(PermissionEnum::Viewer->value);
});
it('throws when the role is invalid', function () {
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $user = $this->createUser();

    new AddAccountMember(
        account: $account,
        user: $user,
        role: 'superhero',
    )->execute();
});
