<?php

declare(strict_types=1);
use App\Actions\SignUp;
use App\Enums\PermissionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('signs up a user with their own owner account', function () {
    Queue::fake();

    $user = new SignUp(
        email: 'chandler.bing@friends.com',
        password: 'password',
        firstName: 'Chandler',
        lastName: 'Bing',
    )->execute();

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'chandler.bing@friends.com',
    ]);

    expect($user->accounts()->get())->toHaveCount(1);

    $account = $user->accounts()->firstOrFail();
    expect($account->name)->toBe('Chandler Bing');
    expect($account->pivot->role)->toBe(PermissionEnum::Owner->value);
    expect(Account::query()->count())->toBe(1);

    Queue::assertPushedOn(queue: 'low', job: LogUserAction::class);
});
