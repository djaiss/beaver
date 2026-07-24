<?php

declare(strict_types=1);
use App\Actions\CreateCatalogType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a type and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $catalogType = new CreateCatalogType(
        user: $editor,
        account: $account,
        name: 'Comics',
        color: '#1D4ED8',
    )->execute();

    expect($catalogType)->toBeInstanceOf(CatalogType::class);
    expect($catalogType->name)->toBe('Comics');
    expect($catalogType->color)->toBe('#1D4ED8');
    expect($catalogType->account_id)->toBe($account->id);

    $this->assertDatabaseHas('types', [
        'id' => $catalogType->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($catalogType->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogTypeCreation,
    );
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $catalogType = new CreateCatalogType(
        user: $owner,
        account: $account,
        name: '<strong>Vinyl</strong>',
    )->execute();

    expect($catalogType->name)->toBe('Vinyl');
});

it('throws when the color is not a valid hex', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    new CreateCatalogType(
        user: $owner,
        account: $account,
        name: 'Comics',
        color: 'blue',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateCatalogType(
        user: $viewer,
        account: $account,
        name: 'Comics',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateCatalogType(
        user: $stranger,
        account: $account,
        name: 'Comics',
    )->execute();
});
