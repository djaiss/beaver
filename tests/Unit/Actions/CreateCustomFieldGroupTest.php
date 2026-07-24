<?php

declare(strict_types=1);
use App\Actions\CreateCustomFieldGroup;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a group and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $group = new CreateCustomFieldGroup(
        user: $editor,
        catalogType: $catalogType,
        name: 'Publishing info',
    )->execute();

    expect($group)->toBeInstanceOf(CustomFieldGroup::class);
    expect($group->name)->toBe('Publishing info');
    expect($group->type_id)->toBe($catalogType->id);
    expect($group->created_by_name)->toBe('Ross Geller');

    $this->assertDatabaseHas('custom_field_groups', ['id' => $group->id, 'type_id' => $catalogType->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldGroupCreation,
    );
});

it('auto-increments the position within the collection type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $first = new CreateCustomFieldGroup(user: $owner, catalogType: $catalogType, name: 'Main')->execute();
    $second = new CreateCustomFieldGroup(user: $owner, catalogType: $catalogType, name: 'Details')->execute();

    expect($first->position)->toBe(1);
    expect($second->position)->toBe(2);
});

it('counts the positions of each type separately', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $comics = CatalogType::factory()->create(['account_id' => $account->id]);
    $wine = CatalogType::factory()->create(['account_id' => $account->id]);

    new CreateCustomFieldGroup(user: $owner, catalogType: $comics, name: 'Publishing info')->execute();
    $first = new CreateCustomFieldGroup(user: $owner, catalogType: $wine, name: 'Origin')->execute();

    expect($first->position)->toBe(1);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new CreateCustomFieldGroup(
        user: $viewer,
        catalogType: $catalogType,
        name: 'Publishing info',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $stranger = $this->createUser();
    $catalogType = CatalogType::factory()->create();

    new CreateCustomFieldGroup(
        user: $stranger,
        catalogType: $catalogType,
        name: 'Publishing info',
    )->execute();
});
