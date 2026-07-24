<?php

declare(strict_types=1);
use App\Actions\DestroyCatalogType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new DestroyCatalogType(
        user: $owner,
        catalogType: $catalogType,
    )->execute();

    $this->assertDatabaseMissing('types', ['id' => $catalogType->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogTypeDeletion,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new DestroyCatalogType(
        user: $viewer,
        catalogType: $catalogType,
    )->execute();
});
