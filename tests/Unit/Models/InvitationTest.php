<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $account = $this->createAccount();
    $invitation = Invitation::factory()->create(['account_id' => $account->id]);

    expect($invitation->account()->exists())->toBeTrue();
    expect($invitation->account)->toBeInstanceOf(Account::class);
});

it('belongs to the user who sent it', function () {
    $inviter = $this->createUser();
    $invitation = Invitation::factory()->create(['invited_by' => $inviter->id]);

    expect($invitation->invitedBy()->exists())->toBeTrue();
    expect($invitation->invitedBy)->toBeInstanceOf(User::class);
    expect($invitation->invitedBy->id)->toBe($inviter->id);
});

it('knows when it is expired', function () {
    $pending = Invitation::factory()->create();
    $expired = Invitation::factory()->expired()->create();

    expect($pending->isExpired())->toBeFalse();
    expect($expired->isExpired())->toBeTrue();
});

it('is pending when not accepted and not expired', function () {
    $invitation = Invitation::factory()->create();

    expect($invitation->isPending())->toBeTrue();
});

it('is not pending when expired', function () {
    $invitation = Invitation::factory()->expired()->create();

    expect($invitation->isPending())->toBeFalse();
});

it('is not pending when accepted', function () {
    $invitation = Invitation::factory()->accepted()->create();

    expect($invitation->isPending())->toBeFalse();
});
