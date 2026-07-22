<?php

declare(strict_types=1);
use App\Actions\UpdateCustomFieldGroup;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('renames a group and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $collectionType->id, 'name' => 'Main']);

    $group = new UpdateCustomFieldGroup(
        user: $editor,
        customFieldGroup: $group,
        name: 'Publishing info',
    )->execute();

    expect($group->name)->toBe('Publishing info');
    expect($group->updated_by_name)->toBe('Monica Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldGroupUpdate,
    );
});

it('leaves the position and the fields of the group alone', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $collectionType->id, 'position' => 3]);

    new UpdateCustomFieldGroup(user: $owner, customFieldGroup: $group, name: 'Origin')->execute();

    expect($group->fresh()->position)->toBe(3);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $collectionType->id]);

    new UpdateCustomFieldGroup(
        user: $viewer,
        customFieldGroup: $group,
        name: 'Publishing info',
    )->execute();
});
