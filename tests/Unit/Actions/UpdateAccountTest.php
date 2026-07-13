<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateAccount;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renames_an_account_for_an_owner(): void
    {
        Queue::fake();

        $account = $this->createAccount(name: 'Old name');
        $owner = $this->createUser(['first_name' => 'Joey', 'last_name' => 'Tribbiani']);
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $result = new UpdateAccount(
            user: $owner,
            account: $account,
            name: 'Central Perk',
        )->execute();

        $this->assertInstanceOf(Account::class, $result);
        $this->assertSame('Central Perk', $account->fresh()->name);
        $this->assertSame($owner->id, $account->fresh()->updated_by_id);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::AccountUpdate,
        );
    }

    #[Test]
    public function it_throws_when_the_user_is_not_an_owner(): void
    {
        Queue::fake();
        $this->expectException(ModelNotFoundException::class);

        $account = $this->createAccount();
        $viewer = $this->createUser();
        $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

        new UpdateAccount(
            user: $viewer,
            account: $account,
            name: 'Central Perk',
        )->execute();
    }
}
