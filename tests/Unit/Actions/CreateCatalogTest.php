<?php

declare(strict_types=1);
use App\Actions\CreateCatalog;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a collection and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $catalog = new CreateCatalog(
        user: $editor,
        account: $account,
        name: 'Marvel Comics 1990s',
        description: 'My run of 90s Marvel',
        emoji: '📚',
        visibility: VisibilityEnum::Shared->value,
        currency: 'USD',
    )->execute();

    expect($catalog)->toBeInstanceOf(Catalog::class);
    expect($catalog->name)->toBe('Marvel Comics 1990s');
    expect($catalog->description)->toBe('My run of 90s Marvel');
    expect($catalog->emoji)->toBe('📚');
    expect($catalog->visibility)->toBe(VisibilityEnum::Shared);
    expect($catalog->account_id)->toBe($account->id);
    expect($catalog->uuid)->not->toBeEmpty();

    $this->assertDatabaseHas('catalogs', [
        'id' => $catalog->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($catalog->created_by_name)->toBe('Ross Geller');
    expect($catalog->updated_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogCreation,
    );
});

it('sanitizes the name, description and emoji', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $catalog = new CreateCatalog(
        user: $owner,
        account: $account,
        name: '<strong>Vinyl Records</strong>',
        description: '<em>Rare pressings</em>',
        emoji: '<script>alert(1)</script>💿',
    )->execute();

    expect($catalog->name)->toBe('Vinyl Records');
    expect($catalog->description)->toBe('Rare pressings');
    expect($catalog->emoji)->toBe('💿');
});

it('defaults the visibility to private', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $catalog = new CreateCatalog(
        user: $owner,
        account: $account,
        name: 'Wine Cellar',
    )->execute();

    expect($catalog->visibility)->toBe(VisibilityEnum::Private);
});

it('throws when the visibility is invalid', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new CreateCatalog(
        user: $owner,
        account: $account,
        name: 'Wine Cellar',
        visibility: 'secret',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateCatalog(
        user: $viewer,
        account: $account,
        name: 'Wine Cellar',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateCatalog(
        user: $stranger,
        account: $account,
        name: 'Wine Cellar',
    )->execute();
});

it('attaches the given collection types', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $comics = CatalogType::factory()->create(['account_id' => $account->id, 'name' => 'Comics']);
    $vinyl = CatalogType::factory()->create(['account_id' => $account->id, 'name' => 'Vinyl Records']);

    $catalog = new CreateCatalog(
        user: $owner,
        account: $account,
        name: 'Wine Cellar',
        catalogTypeIds: [$comics->id, $vinyl->id],
    )->execute();

    expect($catalog->catalogTypes->pluck('id')->sort()->values()->all())->toBe([$comics->id, $vinyl->id]);
});

it('ignores collection type ids belonging to another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $foreignType = CatalogType::factory()->create(['name' => 'Foreign type']);

    $catalog = new CreateCatalog(
        user: $owner,
        account: $account,
        name: 'Wine Cellar',
        catalogTypeIds: [$foreignType->id],
    )->execute();

    expect($catalog->catalogTypes)->toBeEmpty();
});
