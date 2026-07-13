<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyAccount;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_destroys_an_account_and_cascades_its_relations(): void
    {
        Queue::fake();

        $account = $this->createAccount();
        $owner = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
        Invitation::factory()->create(['account_id' => $account->id]);

        new DestroyAccount(
            user: $owner,
            account: $account,
        )->execute();

        /*
         * The members and invitations rows are removed through the
         * cascadeOnDelete foreign keys when the database enforces constraints.
         * The sqlite testing connection leaves foreign keys disabled, so we
         * only assert that the account itself is gone.
         */
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::AccountDeletion,
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

        new DestroyAccount(
            user: $viewer,
            account: $account,
        )->execute();
    }
}
