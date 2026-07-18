<?php

declare(strict_types=1);
use App\Actions\CreateSet;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a set and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $set = new CreateSet(
        user: $editor,
        account: $account,
        name: 'Amazing Spider-Man #1-10',
        description: 'The first ten issues.',
    )->execute();

    expect($set)->toBeInstanceOf(Set::class);
    expect($set->name)->toBe('Amazing Spider-Man #1-10');
    expect($set->description)->toBe('The first ten issues.');
    expect($set->account_id)->toBe($account->id);

    $this->assertDatabaseHas('sets', [
        'id' => $set->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($set->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SetCreation,
    );
});

it('creates a set without a description', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $set = new CreateSet(
        user: $owner,
        account: $account,
        name: 'Vinyl classics',
    )->execute();

    expect($set->description)->toBeNull();
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $set = new CreateSet(
        user: $owner,
        account: $account,
        name: '<strong>Vinyl classics</strong>',
    )->execute();

    expect($set->name)->toBe('Vinyl classics');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateSet(
        user: $viewer,
        account: $account,
        name: 'Vinyl classics',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateSet(
        user: $stranger,
        account: $account,
        name: 'Vinyl classics',
    )->execute();
});
