<?php

declare(strict_types=1);
use App\Actions\RestoreFromTrash;
use App\Enums\PermissionEnum;
use App\Enums\TrashableEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('restores a soft deleted collection', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'name' => 'Vintage Vinyl']);
    $catalog->delete();

    $restored = new RestoreFromTrash(
        user: $owner,
        account: $account,
        type: TrashableEnum::Catalog,
        objectId: $catalog->id,
    )->execute();

    expect($restored)->toBeInstanceOf(Catalog::class);
    expect($catalog->fresh()->deleted_at)->toBeNull();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::TrashRestoration,
    );
});

it('clears who deleted the object once it is restored', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $set = Set::factory()->forAccount($account->id)->create();

    $this->actingAs($editor);
    $set->delete();

    expect($set->fresh()->deleted_by_name)->toBe('Monica Geller');

    new RestoreFromTrash(
        user: $editor,
        account: $account,
        type: TrashableEnum::Set,
        objectId: $set->id,
    )->execute();

    expect($set->fresh()->deleted_by_id)->toBeNull();
    expect($set->fresh()->deleted_by_name)->toBeNull();
});

it('restores an item reached through its collection', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    new RestoreFromTrash(
        user: $owner,
        account: $account,
        type: TrashableEnum::Item,
        objectId: $item->id,
    )->execute();

    expect($item->fresh()->deleted_at)->toBeNull();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $catalog->delete();

    new RestoreFromTrash(
        user: $viewer,
        account: $account,
        type: TrashableEnum::Catalog,
        objectId: $catalog->id,
    )->execute();
});

it('throws when the object belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $otherAccount = $this->createAccount('Moondance Diner');
    $catalog = Catalog::factory()->create(['account_id' => $otherAccount->id]);
    $catalog->delete();

    new RestoreFromTrash(
        user: $owner,
        account: $account,
        type: TrashableEnum::Catalog,
        objectId: $catalog->id,
    )->execute();
});
