<?php

declare(strict_types=1);
use App\Actions\UpdateSeries;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Series;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a series and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $series = Series::factory()->create(['account_id' => $account->id, 'name' => 'Harry Poter']);

    $series = new UpdateSeries(
        user: $editor,
        series: $series,
        name: 'Harry Potter',
        description: 'The wizarding world.',
    )->execute();

    expect($series->name)->toBe('Harry Potter');
    expect($series->description)->toBe('The wizarding world.');
    expect($series->updated_by_name)->toBe('Monica Geller');

    $this->assertDatabaseHas('series', [
        'id' => $series->id,
        'updated_by_id' => $editor->id,
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SeriesUpdate,
    );
});

it('clears the description when none is given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $series = Series::factory()->create(['account_id' => $account->id, 'description' => 'Something.']);

    $series = new UpdateSeries(
        user: $owner,
        series: $series,
        name: 'Star Wars',
    )->execute();

    expect($series->description)->toBeNull();
});

it('refuses a viewer', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $series = Series::factory()->create(['account_id' => $account->id]);

    new UpdateSeries(
        user: $viewer,
        series: $series,
        name: 'Marvel',
    )->execute();
})->throws(ModelNotFoundException::class);

it('refuses someone from another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $series = Series::factory()->create(['account_id' => $account->id]);
    $stranger = $this->createUser();

    new UpdateSeries(
        user: $stranger,
        series: $series,
        name: 'Marvel',
    )->execute();
})->throws(ModelNotFoundException::class);
