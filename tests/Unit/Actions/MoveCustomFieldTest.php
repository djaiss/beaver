<?php

declare(strict_types=1);
use App\Actions\MoveCustomField;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('moves a field down by swapping positions with its neighbour', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'position' => 2]);

    new MoveCustomField(user: $editor, customField: $first, direction: 'down')->execute();

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldUpdate,
    );
});

it('does nothing when moving the first field up', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'position' => 2]);

    new MoveCustomField(user: $owner, customField: $first, direction: 'up')->execute();

    expect($first->refresh()->position)->toBe(1);
    expect($second->refresh()->position)->toBe(2);
});

it('throws when a viewer tries to move a field', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);

    new MoveCustomField(user: $viewer, customField: $field, direction: 'up')->execute();
});

it('swaps within the group rather than stealing a field from a neighbouring group', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $publishing = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $grading = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 2]);

    // Positions restart in every group, so both groups hold a field at 1 and 2.
    $lastOfPublishing = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $publishing->id, 'position' => 2]);
    $firstOfGrading = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $grading->id, 'position' => 1]);
    $secondOfGrading = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $grading->id, 'position' => 2]);

    // The first field of a group has nowhere to go up: the move stops at the boundary.
    new MoveCustomField(user: $owner, customField: $firstOfGrading, direction: 'up')->execute();

    expect($firstOfGrading->refresh()->position)->toBe(1);
    expect($firstOfGrading->refresh()->group_id)->toBe($grading->id);
    expect($lastOfPublishing->refresh()->position)->toBe(2);
    expect($lastOfPublishing->refresh()->group_id)->toBe($publishing->id);

    // Within the group, it still swaps normally.
    new MoveCustomField(user: $owner, customField: $firstOfGrading, direction: 'down')->execute();

    expect($firstOfGrading->refresh()->position)->toBe(2);
    expect($secondOfGrading->refresh()->position)->toBe(1);
});

it('does not swap a standalone field with a grouped one', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    $standalone = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => null, 'position' => 1]);
    $grouped = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $group->id, 'position' => 2]);

    new MoveCustomField(user: $owner, customField: $standalone, direction: 'down')->execute();

    expect($standalone->refresh()->position)->toBe(1);
    expect($standalone->refresh()->group_id)->toBeNull();
    expect($grouped->refresh()->position)->toBe(2);
});
