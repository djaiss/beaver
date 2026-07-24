<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
use App\Models\CustomFieldGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('moves a group down', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 2]);

    $this->actingAs($user)->put('/settings/types/'.$type->id.'/groups/'.$first->id.'/order', [
        'direction' => 'down',
    ])->assertRedirect('/settings/types/'.$type->id.'/edit');

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);
});

it('validates the direction', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    $this->actingAs($user)->put('/settings/types/'.$type->id.'/groups/'.$group->id.'/order', [
        'direction' => 'sideways',
    ])->assertSessionHasErrors('direction');
});

it('cannot reorder a group of another accounts type', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreignType = CatalogType::factory()->create();
    $foreignGroup = CustomFieldGroup::factory()->create(['type_id' => $foreignType->id, 'position' => 1]);

    $this->actingAs($user)->put('/settings/types/'.$foreignType->id.'/groups/'.$foreignGroup->id.'/order', [
        'direction' => 'down',
    ])->assertNotFound();
});

it('forbids a viewer from reordering a group', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 1]);

    $this->actingAs($viewer)->put('/settings/types/'.$type->id.'/groups/'.$group->id.'/order', [
        'direction' => 'down',
    ])->assertNotFound();
});
