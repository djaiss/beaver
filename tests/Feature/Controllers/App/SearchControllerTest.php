<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires a signed in user', function () {
    $this->get('/search')->assertRedirect('/login');
});

it('requires a verified email address', function () {
    $user = $this->createUser(['email_verified_at' => null]);

    $this->actingAs($user)->get('/search')->assertRedirect('/verify-email');
});

it('shows what is indexed before anything is typed', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Central Perk Mugs']);

    $response = $this->actingAs($user)->get('/search');

    $response->assertOk();
    $response->assertSee('Search across your account.');
    $response->assertSee("What's indexed");
});

it('finds an item by name', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man']);

    $response = $this->actingAs($user)->get('/search?q=spider');

    $response->assertOk();
    $response->assertSee('Amazing Spider-Man');
});

it('narrows the results to one kind of record', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Spider']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider Sense']);

    $response = $this->actingAs($user)->get('/search/collections?q=spider');

    $response->assertOk();
    $response->assertSee('Spider');
    $response->assertDontSee('Spider Sense');
});

it('rejects an unknown kind of record', function () {
    $user = $this->createUser();

    $this->actingAs($user)->get('/search/spaceships?q=spider')->assertNotFound();
});

it('says a query of single letters is too short', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man']);

    $response = $this->actingAs($user)->get('/search?q=a');

    $response->assertOk();
    $response->assertSee('Query too short');
    $response->assertDontSee('Amazing Spider-Man');
});

it('says nothing matched when nothing does', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/search?q=gunther');

    $response->assertOk();
    $response->assertSee('Nothing matched');
});

it('does not find another accounts records', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['name' => 'Foreign Collection']);

    $response = $this->actingAs($user)->get('/search?q=foreign');

    $response->assertOk();
    $response->assertDontSee('Foreign Collection');
});

it('lets a viewer search', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Catalog::factory()->create(['account_id' => $account->id, 'name' => 'Central Perk Mugs']);

    $this->actingAs($viewer)->get('/search?q=perk')
        ->assertOk()
        ->assertSee('Central Perk Mugs');
});

it('does not offer a viewer a tag it cannot open', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Tag::factory()->create(['account_id' => $account->id, 'name' => 'Autographed']);

    $this->actingAs($viewer)->get('/search?q=autographed')
        ->assertOk()
        ->assertDontSee('Autographed');
});

it('rejects a query longer than the field allows', function () {
    $user = $this->createUser();

    $this->actingAs($user)->get('/search?q='.str_repeat('a', 256))
        ->assertSessionHasErrors('q');
});
