<?php

declare(strict_types=1);
use App\Actions\MoveCustomField;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\CustomField;
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
