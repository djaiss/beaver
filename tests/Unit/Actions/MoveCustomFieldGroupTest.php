<?php

declare(strict_types=1);
use App\Actions\MoveCustomFieldGroup;
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('moves a group up', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $first = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id, 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id, 'position' => 2]);

    new MoveCustomFieldGroup(user: $owner, customFieldGroup: $second, direction: 'up')->execute();

    expect($second->fresh()->position)->toBe(1);
    expect($first->fresh()->position)->toBe(2);
});

it('moves a group down', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $first = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id, 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id, 'position' => 2]);

    new MoveCustomFieldGroup(user: $owner, customFieldGroup: $first, direction: 'down')->execute();

    expect($first->fresh()->position)->toBe(2);
    expect($second->fresh()->position)->toBe(1);
});

it('does nothing when the group is already at the top', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $first = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id, 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id, 'position' => 2]);

    new MoveCustomFieldGroup(user: $owner, customFieldGroup: $first, direction: 'up')->execute();

    expect($first->fresh()->position)->toBe(1);
    expect($second->fresh()->position)->toBe(2);
});

it('does not swap with a group of another type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $comics = CatalogType::factory()->create(['account_id' => $account->id]);
    $wine = CatalogType::factory()->create(['account_id' => $account->id]);

    $mine = CustomFieldGroup::factory()->create(['type_id' => $comics->id, 'position' => 1]);
    $foreign = CustomFieldGroup::factory()->create(['type_id' => $wine->id, 'position' => 2]);

    new MoveCustomFieldGroup(user: $owner, customFieldGroup: $mine, direction: 'down')->execute();

    expect($mine->fresh()->position)->toBe(1);
    expect($foreign->fresh()->position)->toBe(2);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    new MoveCustomFieldGroup(user: $viewer, customFieldGroup: $group, direction: 'up')->execute();
});
