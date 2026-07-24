<?php

declare(strict_types=1);
use App\Actions\UpdateCatalog;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a collection and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalog = Catalog::factory()->create([
        'account_id' => $account->id,
        'name' => 'Old name',
        'visibility' => VisibilityEnum::Private->value,
    ]);

    $result = new UpdateCatalog(
        user: $editor,
        catalog: $catalog,
        name: 'Marvel Comics 1990s',
        description: 'Updated description',
        emoji: '🦸',
        visibility: VisibilityEnum::Public->value,
        currency: 'EUR',
        settings: ['theme' => 'dark'],
    )->execute();

    expect($result)->toBeInstanceOf(Catalog::class);
    expect($catalog->fresh()->name)->toBe('Marvel Comics 1990s');
    expect($catalog->fresh()->description)->toBe('Updated description');
    expect($catalog->fresh()->emoji)->toBe('🦸');
    expect($catalog->fresh()->visibility)->toBe(VisibilityEnum::Public);
    expect($catalog->fresh()->currency)->toBe('EUR');
    expect($catalog->fresh()->settings)->toBe(['theme' => 'dark']);
    expect($catalog->fresh()->updated_by_id)->toBe($editor->id);
    expect($catalog->fresh()->updated_by_name)->toBe('Monica Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogUpdate,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    new UpdateCatalog(
        user: $viewer,
        catalog: $catalog,
        name: 'New name',
    )->execute();
});

it('syncs the collection types when ids are given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $stale = CatalogType::factory()->create(['account_id' => $account->id]);
    $wanted = CatalogType::factory()->create(['account_id' => $account->id]);
    $catalog->catalogTypes()->attach($stale->id);

    new UpdateCatalog(
        user: $owner,
        catalog: $catalog,
        name: 'Comics',
        visibility: VisibilityEnum::Shared->value,
        catalogTypeIds: [$wanted->id],
    )->execute();

    expect($catalog->fresh()->catalogTypes->pluck('id')->all())->toBe([$wanted->id]);
});

it('ignores a type belonging to another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $foreign = CatalogType::factory()->create();

    new UpdateCatalog(
        user: $owner,
        catalog: $catalog,
        name: 'Comics',
        visibility: VisibilityEnum::Shared->value,
        catalogTypeIds: [$foreign->id],
    )->execute();

    expect($catalog->fresh()->catalogTypes)->toBeEmpty();
});

// The API does not manage types, so it must be able to update a collection without
// disturbing the links the web screen set up.
it('leaves the collection types alone when no ids are given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $catalog->catalogTypes()->attach($type->id);

    new UpdateCatalog(
        user: $owner,
        catalog: $catalog,
        name: 'Comics',
        visibility: VisibilityEnum::Shared->value,
    )->execute();

    expect($catalog->fresh()->catalogTypes->pluck('id')->all())->toBe([$type->id]);
});
