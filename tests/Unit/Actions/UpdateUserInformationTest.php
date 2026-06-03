<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateUserInformation;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserInformationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_user_information(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'first_name' => 'Monica',
            'last_name' => 'Geller',
            'email' => 'monica.geller@friends.com',
        ]);

        $updatedUser = new UpdateUserInformation(
            user: $user,
            email: 'chandler.bing@friends.com',
            firstName: 'Chandler',
            lastName: 'Bing',
            nickname: 'Chan',
            locale: 'fr',
            timeFormat24h: false,
        )->execute();

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals('chandler.bing@friends.com', $updatedUser->email);
        $this->assertEquals('Chandler', $updatedUser->first_name);
        $this->assertEquals('Bing', $updatedUser->last_name);
        $this->assertEquals('Chan', $updatedUser->nickname);
        $this->assertEquals('fr', $updatedUser->locale);
        $this->assertFalse($updatedUser->time_format_24h);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::PersonalProfileUpdate
                && $job->user->id === $user->id
            ),
        );
    }

    #[Test]
    public function it_triggers_email_verification_when_email_changes(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'chandler.bing@friends.com',
            'email_verified_at' => now(),
        ]);

        new UpdateUserInformation(
            user: $user,
            email: 'ross.geller@friends.com',
            firstName: 'Ross',
            lastName: 'Geller',
            nickname: 'Ross',
            locale: 'fr',
            timeFormat24h: true,
        )->execute();

        Event::assertDispatched(
            event: Registered::class,
            callback: fn (Registered $event): bool => $event->user->email === 'ross.geller@friends.com',
        );
        $this->assertNull($user->refresh()->email_verified_at);
    }

    #[Test]
    public function it_does_not_trigger_email_verification_when_email_stays_same(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'chandler.bing@friends.com',
            'email_verified_at' => now(),
        ]);

        new UpdateUserInformation(
            user: $user,
            email: 'chandler.bing@friends.com',
            firstName: 'Ross',
            lastName: 'Geller',
            nickname: 'Ross',
            locale: 'fr',
            timeFormat24h: true,
        )->execute();

        Event::assertNotDispatched(Registered::class);
        $this->assertNotNull($user->refresh()->email_verified_at);
    }
}
