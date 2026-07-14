<?php

declare(strict_types=1);
use App\Actions\UpdateUserInformation;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates user information', function () {
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
        locale: 'fr_FR',
        timeFormat24h: false,
    )->execute();

    expect($updatedUser)->toBeInstanceOf(User::class);

    expect($updatedUser->email)->toEqual('chandler.bing@friends.com');
    expect($updatedUser->first_name)->toEqual('Chandler');
    expect($updatedUser->last_name)->toEqual('Bing');
    expect($updatedUser->nickname)->toEqual('Chan');
    expect($updatedUser->locale)->toEqual('fr_FR');
    expect($updatedUser->time_format_24h)->toBeFalse();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::PersonalProfileUpdate
            && $job->user->id === $user->id
        ),
    );
});

it('triggers email verification when email changes', function () {
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
        locale: 'fr_FR',
        timeFormat24h: true,
    )->execute();

    Event::assertDispatched(
        event: Registered::class,
        callback: fn (Registered $event): bool => $event->user->email === 'ross.geller@friends.com',
    );
    expect($user->refresh()->email_verified_at)->toBeNull();
});

it('does not trigger email verification when email stays same', function () {
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
        locale: 'fr_FR',
        timeFormat24h: true,
    )->execute();

    Event::assertNotDispatched(Registered::class);
    expect($user->refresh()->email_verified_at)->not->toBeNull();
});
