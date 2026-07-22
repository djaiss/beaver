<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the account locations, nested', function () {
    $user = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Shelf A']);
    Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Box 1', 'parent_id' => $shelf->id]);

    $response = $this->actingAs($user)->get('/locations');

    $response->assertOk();
    $response->assertSee('Shelf A');
    $response->assertSee('Box 1');
});

it('renders the locations title help popover', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/locations');

    $response->assertOk();
    $response->assertSee('Where copies are physically stored');
});

it('does not list another accounts locations', function () {
    $user = $this->createUser();
    Location::factory()->create(['name' => 'Foreign Location']);

    $response = $this->actingAs($user)->get('/locations');

    $response->assertOk();
    $response->assertDontSee('Foreign Location');
});

it('allows a viewer to list locations', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Location::factory()->create(['account_id' => $account->id, 'name' => 'Shelf A']);

    $this->actingAs($viewer)->get('/locations')
        ->assertOk()
        ->assertSee('Shelf A');
});

it('creates a location', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/locations', [
        'name' => 'Shelf A',
    ]);

    $response->assertRedirect('/locations');
    $response->assertSessionHas('status', 'Location created');

    $location = Location::query()->first();
    expect($location)->not->toBeNull();
    expect($location->name)->toBe('Shelf A');
    expect($location->account_id)->toBe($user->account_id);
});

it('creates a nested location from a form-encoded parent id', function () {
    Queue::fake();

    $user = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Shelf A']);

    $response = $this->actingAs($user)->post('/locations', [
        'name' => 'Box 1',
        'parent_id' => (string) $shelf->id,
    ]);

    $response->assertRedirect('/locations');

    $box = Location::query()->where('parent_id', $shelf->id)->first();
    expect($box)->not->toBeNull();
    expect($box->name)->toBe('Box 1');
});

it('creates a location with an emoji', function () {
    Queue::fake();

    $user = $this->createUser();

    $this->actingAs($user)->post('/locations', [
        'name' => 'Garage',
        'emoji' => '🚪',
    ]);

    $location = Location::query()->first();
    expect($location->emoji)->toBe('🚪');
});

it('rejects an emoji outside the allowed set', function () {
    $user = $this->createUser();

    $this->actingAs($user)->post('/locations', [
        'name' => 'Garage',
        'emoji' => '💩',
    ])->assertSessionHasErrors('emoji');
});

it('forbids viewers from creating a location', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->post('/locations', [
        'name' => 'Shelf A',
    ])->assertNotFound();
});

it('validates the name is required when creating', function () {
    $user = $this->createUser();

    $this->actingAs($user)->post('/locations', [])
        ->assertSessionHasErrors('name');
});

it('updates a location', function () {
    Queue::fake();

    $user = $this->createUser();
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Old name']);

    $response = $this->actingAs($user)->put('/locations/'.$location->id, [
        'name' => 'Shelf A',
    ]);

    $response->assertRedirect('/locations');
    $response->assertSessionHas('status', 'Location updated');
    expect($location->fresh()->name)->toBe('Shelf A');
});

it('moves a location under a new parent from a form-encoded parent id', function () {
    Queue::fake();

    $user = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Shelf A']);
    $box = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Box 1']);

    $response = $this->actingAs($user)->put('/locations/'.$box->id, [
        'name' => 'Box 1',
        'parent_id' => (string) $shelf->id,
    ]);

    $response->assertRedirect('/locations');
    expect($box->fresh()->parent_id)->toBe($shelf->id);
});

it('cannot update another accounts location', function () {
    $user = $this->createUser();
    $foreign = Location::factory()->create();

    $this->actingAs($user)->put('/locations/'.$foreign->id, [
        'name' => 'Shelf A',
    ])->assertNotFound();
});

it('deletes a location', function () {
    Queue::fake();

    $user = $this->createUser();
    $location = Location::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->delete('/locations/'.$location->id);

    $response->assertRedirect('/locations');
    $this->assertModelMissing($location);
});

it('cannot delete another accounts location', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreign = Location::factory()->create();

    $this->actingAs($user)->delete('/locations/'.$foreign->id)->assertNotFound();
    $this->assertModelExists($foreign);
});
