<?php

declare(strict_types=1);
use App\Actions\SyncCatalogTypeCatalogs;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('syncs the collections a type is linked to', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $comics = Catalog::factory()->create(['account_id' => $account->id]);
    $vinyl = Catalog::factory()->create(['account_id' => $account->id]);

    new SyncCatalogTypeCatalogs(user: $editor, catalogType: $type, catalogIds: [$comics->id, $vinyl->id])->execute();

    expect($type->catalogs()->pluck('catalogs.id')->all())->toEqualCanonicalizing([$comics->id, $vinyl->id]);

    new SyncCatalogTypeCatalogs(user: $editor, catalogType: $type, catalogIds: [$comics->id])->execute();

    expect($type->catalogs()->pluck('catalogs.id')->all())->toBe([$comics->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogTypeUpdate,
    );
});

it('ignores collections that belong to another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $foreign = Catalog::factory()->create();

    new SyncCatalogTypeCatalogs(user: $owner, catalogType: $type, catalogIds: [$foreign->id])->execute();

    expect($type->catalogs()->count())->toBe(0);
});

it('throws when a viewer tries to sync collections', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $type = CatalogType::factory()->create(['account_id' => $account->id]);

    new SyncCatalogTypeCatalogs(user: $viewer, catalogType: $type, catalogIds: [])->execute();
});
