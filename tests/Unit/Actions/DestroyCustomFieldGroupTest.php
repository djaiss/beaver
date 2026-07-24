<?php

declare(strict_types=1);
use App\Actions\DestroyCustomFieldGroup;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a group', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    new DestroyCustomFieldGroup(
        user: $editor,
        customFieldGroup: $group,
    )->execute();

    $this->assertModelMissing($group);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldGroupDeletion,
    );
});

it('keeps the fields of the group and drops them back to ungrouped', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    $field = CustomField::factory()->create([
        'type_id' => $catalogType->id,
        'group_id' => $group->id,
        'name' => 'Grade',
    ]);

    new DestroyCustomFieldGroup(user: $owner, customFieldGroup: $group)->execute();

    // No value recorded against the field is lost: it survives, ungrouped.
    $this->assertModelExists($field);
    expect($field->fresh()->group_id)->toBeNull();
    expect($field->fresh()->name)->toBe('Grade');
    expect($catalogType->ungroupedCustomFields()->get()->map->name->all())->toBe(['Grade']);
});

it('leaves the fields of another group alone', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $doomed = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);
    $survivor = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    $field = CustomField::factory()->create(['type_id' => $catalogType->id, 'group_id' => $survivor->id]);

    new DestroyCustomFieldGroup(user: $owner, customFieldGroup: $doomed)->execute();

    expect($field->fresh()->group_id)->toBe($survivor->id);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    new DestroyCustomFieldGroup(
        user: $viewer,
        customFieldGroup: $group,
    )->execute();
});
