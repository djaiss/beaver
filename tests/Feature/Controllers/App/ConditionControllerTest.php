<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the account conditions', function () {
    $user = $this->createUser();
    Condition::factory()->create(['account_id' => $user->account_id, 'name' => 'New']);

    $response = $this->actingAs($user)->get('/settings/conditions');

    $response->assertOk();
    $response->assertSee('New');
});

it('does not list another accounts conditions', function () {
    $user = $this->createUser();
    Condition::factory()->create(['name' => 'Foreign Condition']);

    $response = $this->actingAs($user)->get('/settings/conditions');

    $response->assertOk();
    $response->assertDontSee('Foreign Condition');
});

it('does not list system default conditions', function () {
    $user = $this->createUser();
    Condition::factory()->systemDefault()->create(['name' => 'System Default']);

    $response = $this->actingAs($user)->get('/settings/conditions');

    $response->assertOk();
    $response->assertDontSee('System Default');
});

it('forbids viewers from listing conditions', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->get('/settings/conditions')->assertNotFound();
});

it('allows an editor to list conditions', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    Condition::factory()->create(['account_id' => $account->id, 'name' => 'New']);

    $this->actingAs($editor)->get('/settings/conditions')
        ->assertOk()
        ->assertSee('New');
});

it('creates a condition', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/settings/conditions', [
        'name' => 'New',
    ]);

    $response->assertRedirect('/settings/conditions');
    $response->assertSessionHas('status', 'Condition created');

    $condition = Condition::query()->first();
    expect($condition)->not->toBeNull();
    expect($condition->name)->toBe('New');
    expect($condition->account_id)->toBe($user->account_id);
});

it('validates the name is required when creating', function () {
    $user = $this->createUser();

    $this->actingAs($user)->post('/settings/conditions', [])
        ->assertSessionHasErrors('name');
});

it('forbids viewers from creating a condition', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->post('/settings/conditions', [
        'name' => 'New',
    ])->assertNotFound();
});

it('updates a condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $condition = Condition::factory()->create(['account_id' => $user->account_id, 'name' => 'Old name']);

    $response = $this->actingAs($user)->put('/settings/conditions/'.$condition->id, [
        'name' => 'New',
    ]);

    $response->assertRedirect('/settings/conditions');
    $response->assertSessionHas('status', 'Condition updated');
    expect($condition->fresh()->name)->toBe('New');
});

it('cannot update another accounts condition', function () {
    $user = $this->createUser();
    $foreign = Condition::factory()->create();

    $this->actingAs($user)->put('/settings/conditions/'.$foreign->id, [
        'name' => 'New',
    ])->assertNotFound();
});

it('cannot update a system default condition', function () {
    $user = $this->createUser();
    $systemDefault = Condition::factory()->systemDefault()->create();

    $this->actingAs($user)->put('/settings/conditions/'.$systemDefault->id, [
        'name' => 'New',
    ])->assertNotFound();
});

it('deletes a condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $condition = Condition::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->delete('/settings/conditions/'.$condition->id);

    $response->assertRedirect('/settings/conditions');
    $this->assertModelMissing($condition);
});

it('cannot delete another accounts condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreign = Condition::factory()->create();

    $this->actingAs($user)->delete('/settings/conditions/'.$foreign->id)->assertNotFound();
    $this->assertModelExists($foreign);
});

it('cannot delete a system default condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $systemDefault = Condition::factory()->systemDefault()->create();

    $this->actingAs($user)->delete('/settings/conditions/'.$systemDefault->id)->assertNotFound();
    $this->assertModelExists($systemDefault);
});
