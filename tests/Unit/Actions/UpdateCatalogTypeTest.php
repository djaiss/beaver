<?php

declare(strict_types=1);
use App\Actions\UpdateCatalogType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('updates a type and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id, 'name' => 'Old name', 'color' => '#111111']);

    $result = new UpdateCatalogType(
        user: $editor,
        catalogType: $catalogType,
        name: 'Comics',
        color: '#1D4ED8',
    )->execute();

    expect($result)->toBeInstanceOf(CatalogType::class);
    expect($catalogType->fresh()->name)->toBe('Comics');
    expect($catalogType->fresh()->color)->toBe('#1D4ED8');
    expect($catalogType->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CatalogTypeUpdate,
    );
});

it('throws when the color is not a valid hex', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new UpdateCatalogType(
        user: $owner,
        catalogType: $catalogType,
        name: 'Comics',
        color: 'not-a-color',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new UpdateCatalogType(
        user: $viewer,
        catalogType: $catalogType,
        name: 'Comics',
    )->execute();
});
