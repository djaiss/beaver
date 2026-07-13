<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\AccountMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountMemberTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_account(): void
    {
        $account = $this->createAccount();
        $member = AccountMember::factory()->create(['account_id' => $account->id]);

        $this->assertTrue($member->account()->exists());
        $this->assertInstanceOf(Account::class, $member->account);
    }

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $user = $this->createUser();
        $member = AccountMember::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($member->user()->exists());
        $this->assertInstanceOf(User::class, $member->user);
    }

    #[Test]
    public function it_belongs_to_the_user_who_invited_it(): void
    {
        $inviter = $this->createUser();
        $member = AccountMember::factory()->create(['invited_by' => $inviter->id]);

        $this->assertTrue($member->invitedBy()->exists());
        $this->assertInstanceOf(User::class, $member->invitedBy);
        $this->assertSame($inviter->id, $member->invitedBy->id);
    }
}
