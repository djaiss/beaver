<?php

declare(strict_types=1);
use App\Actions\DestroyCatalog;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('soft deletes a collection', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    new DestroyCatalog(
        user: $owner,
        catalog: $catalog,
    )->execute();

    $this->assertSoftDeleted($catalog);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogDeletion,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    new DestroyCatalog(
        user: $viewer,
        catalog: $catalog,
    )->execute();
});
