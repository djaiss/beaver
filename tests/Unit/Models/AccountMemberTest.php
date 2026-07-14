<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $member = AccountMember::factory()->create(['account_id' => $account->id]);

    expect($member->account()->exists())->toBeTrue();
    expect($member->account)->toBeInstanceOf(Account::class);
});
it('belongs to a user', function () {
    $user = $this->createUser();
    $member = AccountMember::factory()->create(['user_id' => $user->id]);

    expect($member->user()->exists())->toBeTrue();
    expect($member->user)->toBeInstanceOf(User::class);
});
it('belongs to the user who invited it', function () {
    $inviter = $this->createUser();
    $member = AccountMember::factory()->create(['invited_by' => $inviter->id]);

    expect($member->invitedBy()->exists())->toBeTrue();
    expect($member->invitedBy)->toBeInstanceOf(User::class);
    expect($member->invitedBy->id)->toBe($inviter->id);
});
