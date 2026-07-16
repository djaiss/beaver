<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the account tags', function () {
    $user = $this->createUser();
    Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);

    $response = $this->actingAs($user)->get('/settings/tags');

    $response->assertOk();
    $response->assertSee('Signed');
});

it('does not list another accounts tags', function () {
    $user = $this->createUser();
    Tag::factory()->create(['name' => 'Foreign Tag']);

    $response = $this->actingAs($user)->get('/settings/tags');

    $response->assertOk();
    $response->assertDontSee('Foreign Tag');
});

it('forbids viewers from listing tags', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->get('/settings/tags')->assertNotFound();
});

it('allows an editor to list tags', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $this->actingAs($editor)->get('/settings/tags')
        ->assertOk()
        ->assertSee('Signed');
});

it('creates a tag', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/settings/tags', [
        'name' => 'Signed',
    ]);

    $response->assertRedirect('/settings/tags');
    $response->assertSessionHas('status', 'Tag created');

    $tag = Tag::query()->first();
    expect($tag)->not->toBeNull();
    expect($tag->name)->toBe('Signed');
    expect($tag->account_id)->toBe($user->account_id);
});

it('validates the name is required when creating', function () {
    $user = $this->createUser();

    $this->actingAs($user)->post('/settings/tags', [])
        ->assertSessionHasErrors('name');
});

it('forbids viewers from creating a tag', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->post('/settings/tags', [
        'name' => 'Signed',
    ])->assertNotFound();
});

it('updates a tag', function () {
    Queue::fake();

    $user = $this->createUser();
    $tag = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Old name']);

    $response = $this->actingAs($user)->put('/settings/tags/'.$tag->id, [
        'name' => 'Signed',
    ]);

    $response->assertRedirect('/settings/tags');
    $response->assertSessionHas('status', 'Tag updated');
    expect($tag->fresh()->name)->toBe('Signed');
});

it('cannot update another accounts tag', function () {
    $user = $this->createUser();
    $foreign = Tag::factory()->create();

    $this->actingAs($user)->put('/settings/tags/'.$foreign->id, [
        'name' => 'Signed',
    ])->assertNotFound();
});

it('deletes a tag', function () {
    Queue::fake();

    $user = $this->createUser();
    $tag = Tag::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->delete('/settings/tags/'.$tag->id);

    $response->assertRedirect('/settings/tags');
    $this->assertModelMissing($tag);
});

it('cannot delete another accounts tag', function () {
    Queue::fake();

    $user = $this->createUser();
    $foreign = Tag::factory()->create();

    $this->actingAs($user)->delete('/settings/tags/'.$foreign->id)->assertNotFound();
    $this->assertModelExists($foreign);
});
