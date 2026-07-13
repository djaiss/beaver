<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\SignUp;
use App\Enums\PermissionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_signs_up_a_user_with_their_own_owner_account(): void
    {
        Queue::fake();

        $user = new SignUp(
            email: 'chandler.bing@friends.com',
            password: 'password',
            firstName: 'Chandler',
            lastName: 'Bing',
        )->execute();

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'chandler.bing@friends.com',
        ]);

        $this->assertCount(1, $user->accounts()->get());

        $account = $user->accounts()->firstOrFail();
        $this->assertSame('Chandler Bing', $account->name);
        $this->assertSame(PermissionEnum::Owner->value, $account->pivot->role);
        $this->assertSame(1, Account::query()->count());

        Queue::assertPushedOn(queue: 'low', job: LogUserAction::class);
    }
}
