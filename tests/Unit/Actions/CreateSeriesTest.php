<?php

declare(strict_types=1);
use App\Actions\CreateSeries;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Series;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a series and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $series = new CreateSeries(
        user: $editor,
        account: $account,
        name: 'Harry Potter',
        description: 'The wizarding world.',
    )->execute();

    expect($series)->toBeInstanceOf(Series::class);
    expect($series->name)->toBe('Harry Potter');
    expect($series->description)->toBe('The wizarding world.');
    expect($series->account_id)->toBe($account->id);

    $this->assertDatabaseHas('series', [
        'id' => $series->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($series->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SeriesCreation,
    );
});

it('creates a series without a description', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $series = new CreateSeries(
        user: $owner,
        account: $account,
        name: 'Star Wars',
    )->execute();

    expect($series->description)->toBeNull();
});

it('sanitizes the name and description', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $series = new CreateSeries(
        user: $owner,
        account: $account,
        name: '  <b>Pink Floyd</b>  ',
        description: '  <i>Studio albums.</i>  ',
    )->execute();

    expect($series->name)->toBe('Pink Floyd');
    expect($series->description)->toBe('Studio albums.');
});

it('refuses a viewer', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateSeries(
        user: $viewer,
        account: $account,
        name: 'Marvel',
    )->execute();
})->throws(ModelNotFoundException::class);

it('refuses someone from another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateSeries(
        user: $stranger,
        account: $account,
        name: 'Marvel',
    )->execute();
})->throws(ModelNotFoundException::class);
