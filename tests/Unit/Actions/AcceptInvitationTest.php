<?php

declare(strict_types=1);
use App\Actions\AcceptInvitation;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('accepts a pending invitation', function () {
    Queue::fake();

    $account = $this->createAccount();
    $user = $this->createUser(['email' => 'ross.geller@friends.com', 'account_id' => $account->id]);
    $invitation = Invitation::factory()->create([
        'account_id' => $account->id,
        'email' => 'ross.geller@friends.com',
    ]);

    new AcceptInvitation(
        invitation: $invitation,
        user: $user,
    )->execute();

    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});
it('throws when the invitation is not pending', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $user = $this->createUser(['email' => 'ross.geller@friends.com']);
    $invitation = Invitation::factory()->expired()->create([
        'email' => 'ross.geller@friends.com',
    ]);

    new AcceptInvitation(
        invitation: $invitation,
        user: $user,
    )->execute();
});
it('throws when the email does not match', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $user = $this->createUser(['email' => 'ross.geller@friends.com']);
    $invitation = Invitation::factory()->create([
        'email' => 'rachel.green@friends.com',
    ]);

    new AcceptInvitation(
        invitation: $invitation,
        user: $user,
    )->execute();
});
