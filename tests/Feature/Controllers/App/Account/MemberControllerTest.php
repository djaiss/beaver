<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Account;

use App\Enums\PermissionEnum;
use App\Mail\AccountInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_the_members_for_an_owner(): void
    {
        $owner = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($owner)->get("accounts/{$account->id}/members");

        $response->assertOk();
        $response->assertViewIs('app.account.members.index');
    }

    #[Test]
    public function it_forbids_a_non_owner_from_listing_the_members(): void
    {
        $user = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

        $response = $this->actingAs($user)->get("accounts/{$account->id}/members");

        $response->assertForbidden();
    }

    #[Test]
    public function it_sends_an_invitation(): void
    {
        Queue::fake();
        Mail::fake();

        $owner = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($owner)->post("accounts/{$account->id}/members", [
            'email' => 'phoebe.buffay@friends.com',
            'role' => PermissionEnum::Editor->value,
        ]);

        $response->assertRedirect(route('accounts.members.index', $account->id, absolute: false));
        $this->assertDatabaseHas('invitations', [
            'account_id' => $account->id,
            'email' => 'phoebe.buffay@friends.com',
            'role' => PermissionEnum::Editor->value,
        ]);
        Mail::assertQueued(AccountInvitation::class);
    }

    #[Test]
    public function it_updates_the_role_of_a_member(): void
    {
        Queue::fake();

        $owner = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $member = $this->assignUserToAccount(
            user: $this->createUser(),
            account: $account,
            role: PermissionEnum::Viewer->value,
        );

        $response = $this->actingAs($owner)->put("accounts/{$account->id}/members/{$member->id}", [
            'role' => PermissionEnum::Editor->value,
        ]);

        $response->assertRedirect(route('accounts.members.index', $account->id, absolute: false));
        $this->assertSame(PermissionEnum::Editor->value, $member->fresh()->role);
    }

    #[Test]
    public function it_removes_a_member(): void
    {
        Queue::fake();

        $owner = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $member = $this->assignUserToAccount(
            user: $this->createUser(),
            account: $account,
            role: PermissionEnum::Viewer->value,
        );

        $response = $this->actingAs($owner)->delete("accounts/{$account->id}/members/{$member->id}");

        $response->assertRedirect(route('accounts.members.index', $account->id, absolute: false));
        $this->assertModelMissing($member);
    }

    #[Test]
    public function it_returns_not_found_for_a_member_of_another_account(): void
    {
        $owner = $this->createUser();
        $account = $this->createAccount();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $otherAccount = $this->createAccount();
        $foreignMember = $this->assignUserToAccount(
            user: $this->createUser(),
            account: $otherAccount,
            role: PermissionEnum::Viewer->value,
        );

        $response = $this->actingAs($owner)->delete("accounts/{$account->id}/members/{$foreignMember->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_cannot_remove_the_last_owner(): void
    {
        Queue::fake();

        $owner = $this->createUser();
        $account = $this->createAccount();
        $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($owner)->delete("accounts/{$account->id}/members/{$member->id}");

        $response->assertSessionHasErrors('member');
        $this->assertDatabaseHas('account_user', ['id' => $member->id]);
    }

    #[Test]
    public function it_cannot_demote_the_last_owner(): void
    {
        Queue::fake();

        $owner = $this->createUser();
        $account = $this->createAccount();
        $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $response = $this->actingAs($owner)->put("accounts/{$account->id}/members/{$member->id}", [
            'role' => PermissionEnum::Viewer->value,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertSame(PermissionEnum::Owner->value, $member->fresh()->role);
    }
}
