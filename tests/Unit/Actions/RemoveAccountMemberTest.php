<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\RemoveAccountMember;
use App\Enums\PermissionEnum;
use App\Jobs\LogUserAction;
use App\Models\AccountMember;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RemoveAccountMemberTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_removes_a_member_from_an_account(): void
    {
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
    }

    #[Test]
    public function it_throws_when_the_user_is_not_an_owner(): void
    {
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
    }

    #[Test]
    public function it_throws_when_the_member_belongs_to_another_account(): void
    {
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
    }

    #[Test]
    public function it_throws_when_removing_the_last_owner(): void
    {
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
    }
}
