<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\TrashableEnum;
use App\Models\Catalog;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('shows the trash screen', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'name' => 'Vintage Vinyl']);
    $catalog->delete();

    $response = $this->actingAs($owner)->get(route('settings.trash.index'));

    $response->assertStatus(200);
    $response->assertSee('Vintage Vinyl');
    $response->assertSee('Empty trash');
});

it('renders the trash title help popover', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get(route('settings.trash.index'));

    $response->assertStatus(200);
    $response->assertSee('kept for a retention period');
});

it('shows an empty trash screen', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get(route('settings.trash.index'));

    $response->assertStatus(200);
    $response->assertSee('The trash is empty');
    $response->assertDontSee('Empty trash');
});

it('restores an object', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();

    $response = $this->actingAs($editor)->put(route('settings.trash.update'), [
        'type' => TrashableEnum::Item->value,
        'id' => $item->id,
    ]);

    $response->assertRedirect(route('settings.trash.index'));
    $response->assertSessionHas('status', 'Restored');
    expect($item->fresh()->deleted_at)->toBeNull();
});

it('validates the type', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->put(route('settings.trash.update'), [
        'type' => 'invitation',
        'id' => 1,
    ]);

    $response->assertSessionHasErrors('type');
});

it('returns not found when the object belongs to another account', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $otherAccount = $this->createAccount('Moondance Diner');
    $catalog = Catalog::factory()->create(['account_id' => $otherAccount->id]);
    $catalog->delete();

    $response = $this->actingAs($owner)->put(route('settings.trash.update'), [
        'type' => TrashableEnum::Catalog->value,
        'id' => $catalog->id,
    ]);

    $response->assertNotFound();
});

it('empties the trash', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $item->delete();
    $catalog->delete();

    $response = $this->actingAs($editor)->delete(route('settings.trash.destroy'));

    $response->assertRedirect(route('settings.trash.index'));
    $response->assertSessionHas('status', 'Trash emptied');
    $this->assertDatabaseMissing('items', ['id' => $item->id]);
    $this->assertDatabaseMissing('catalogs', ['id' => $catalog->id]);
});

it('returns not found when a viewer empties the trash', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($viewer)->delete(route('settings.trash.destroy'));

    $response->assertNotFound();
});

it('returns not found for a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($viewer)->get(route('settings.trash.index'));

    $response->assertNotFound();
});
