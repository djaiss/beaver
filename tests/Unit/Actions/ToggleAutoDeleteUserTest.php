<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ToggleAutoDeleteUser;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToggleAutoDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_enables_auto_delete_user(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'auto_delete_user' => false,
        ]);

        $updatedUser = new ToggleAutoDeleteUser(
            user: $user,
            autoDeleteUser: true,
        )->execute();

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertTrue($updatedUser->auto_delete_user);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::AutoDeleteUserUpdate
                && $job->user->id === $user->id
                && $job->parameters === ['status' => 'enabled']
            ),
        );
    }
}
