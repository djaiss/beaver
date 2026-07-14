<?php

declare(strict_types=1);
use App\Actions\CreateUser;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates an account', function () {
    Queue::fake();

    Date::setTestNow(Date::create(2018, 1, 1));

    $user = new CreateUser(
        email: 'chandler.bing@friends.com',
        password: 'password',
        firstName: 'Chandler',
        lastName: 'Bing',
    )->execute();

    expect($user)->toBeInstanceOf(User::class);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'chandler.bing@friends.com',
        'trial_ends_at' => '2018-01-31 00:00:00',
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::AccountCreation
            && $job->user->id === $user->id
        ),
    );
});
it('cant create an account with the same email', function () {
    User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $this->expectException(UniqueConstraintViolationException::class);

    new CreateUser(
        email: 'chandler.bing@friends.com',
        password: 'password',
        firstName: 'Chandler',
        lastName: 'Bing',
    )->execute();
});
