<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\AddAccountMember;
use App\Enums\PermissionEnum;
use App\Models\AccountMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddAccountMemberTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_adds_a_member_to_an_account(): void
    {
        $account = $this->createAccount();
        $user = $this->createUser();
        $inviter = $this->createUser();

        $member = new AddAccountMember(
            account: $account,
            user: $user,
            role: PermissionEnum::Editor->value,
            invitedBy: $inviter,
        )->execute();

        $this->assertInstanceOf(AccountMember::class, $member);
        $this->assertNotNull($member->joined_at);
        $this->assertDatabaseHas('account_user', [
            'id' => $member->id,
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => PermissionEnum::Editor->value,
            'invited_by' => $inviter->id,
        ]);
    }

    #[Test]
    public function it_defaults_to_the_viewer_role(): void
    {
        $account = $this->createAccount();
        $user = $this->createUser();

        $member = new AddAccountMember(
            account: $account,
            user: $user,
        )->execute();

        $this->assertSame(PermissionEnum::Viewer->value, $member->role);
    }

    #[Test]
    public function it_throws_when_the_role_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $account = $this->createAccount();
        $user = $this->createUser();

        new AddAccountMember(
            account: $account,
            user: $user,
            role: 'superhero',
        )->execute();
    }
}
