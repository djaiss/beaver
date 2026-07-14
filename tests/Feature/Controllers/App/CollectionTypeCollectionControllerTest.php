<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('syncs the collections that use the type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $comics = Collection::factory()->create(['account_id' => $user->account_id]);
    $vinyl = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->put('/settings/types/'.$type->id.'/collections', ['collection_ids' => [$comics->id, $vinyl->id]])
        ->assertRedirect('/settings/types/'.$type->id.'/edit');

    expect($type->collections()->pluck('collections.id')->all())->toEqualCanonicalizing([$comics->id, $vinyl->id]);
});

it('ignores collections from another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->put('/settings/types/'.$type->id.'/collections', ['collection_ids' => [$foreign->id]]);

    expect($type->collections()->count())->toBe(0);
});

it('forbids a viewer from syncing collections', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->put('/settings/types/'.$type->id.'/collections', ['collection_ids' => []])->assertNotFound();
});
