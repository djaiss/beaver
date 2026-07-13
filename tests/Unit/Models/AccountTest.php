<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_users(): void
    {
        $account = $this->createAccount();
        $user = $this->createUser();
        $this->assignUserToAccount(user: $user, account: $account);

        $this->assertTrue($account->users()->exists());
        $this->assertInstanceOf(User::class, $account->users()->first());
    }

    #[Test]
    public function it_has_many_members(): void
    {
        $account = $this->createAccount();
        AccountMember::factory()->create(['account_id' => $account->id]);

        $this->assertTrue($account->members()->exists());
        $this->assertInstanceOf(AccountMember::class, $account->members()->first());
    }

    #[Test]
    public function it_has_many_invitations(): void
    {
        $account = $this->createAccount();
        Invitation::factory()->create(['account_id' => $account->id]);

        $this->assertTrue($account->invitations()->exists());
        $this->assertInstanceOf(Invitation::class, $account->invitations()->first());
    }

    #[Test]
    public function it_lists_only_owners_as_administrators(): void
    {
        $account = $this->createAccount();
        $owner = $this->createUser();
        $viewer = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
        $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

        $administrators = $account->administrators()->get();

        $this->assertCount(1, $administrators);
        $this->assertTrue($administrators->contains('id', $owner->id));
        $this->assertFalse($administrators->contains('id', $viewer->id));
    }

    #[Test]
    public function it_knows_whether_a_user_is_a_member(): void
    {
        $account = $this->createAccount();
        $member = $this->createUser();
        $stranger = $this->createUser();
        $this->assignUserToAccount(user: $member, account: $account);

        $this->assertTrue($account->hasMember($member));
        $this->assertFalse($account->hasMember($stranger));
    }

    #[Test]
    public function it_returns_the_role_for_a_member(): void
    {
        $account = $this->createAccount();
        $owner = $this->createUser();
        $stranger = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $this->assertSame(PermissionEnum::Owner->value, $account->roleFor($owner));
        $this->assertNull($account->roleFor($stranger));
    }

    #[Test]
    public function it_encrypts_the_name_at_rest(): void
    {
        $account = $this->createAccount(name: 'Central Perk');

        $rawName = DB::table('accounts')->where('id', $account->id)->value('name');

        $this->assertNotSame('Central Perk', $rawName);
        $this->assertSame('Central Perk', $account->name);
        $this->assertSame('Central Perk', $account->fresh()->name);
    }
}
