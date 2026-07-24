<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the series of the account', function () {
    $user = $this->createUser();
    Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Harry Potter']);
    Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Star Wars']);

    $response = $this->actingAs($user)->get('/series');

    $response->assertOk()
        ->assertSee('Harry Potter')
        ->assertSee('Star Wars')
        ->assertSee('2 series');
});

it('renders the series title help popover', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/series');

    $response->assertOk()
        ->assertSee('Ties related items into a broader franchise or body of work');
});

it('does not list the series of another account', function () {
    $user = $this->createUser();
    Series::factory()->create(['name' => 'Someone else’s franchise']);

    $this->actingAs($user)->get('/series')
        ->assertOk()
        ->assertDontSee('Someone else’s franchise');
});

it('shows an empty state when there are no series', function () {
    $user = $this->createUser();

    $this->actingAs($user)->get('/series')
        ->assertOk()
        ->assertSee('No series yet');
});

it('counts the collections a series reaches into', function () {
    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Harry Potter']);

    $books = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Books']);
    $lego = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'LEGO']);
    Item::factory()->count(2)->create(['catalog_id' => $books->id, 'series_id' => $series->id]);
    Item::factory()->create(['catalog_id' => $lego->id, 'series_id' => $series->id]);

    $response = $this->actingAs($user)->get('/series');

    $response->assertOk()
        ->assertSee('3 items')
        ->assertSee('2 collections')
        ->assertSee('Books')
        ->assertSee('LEGO');
});

it('shows a series with its items grouped by collection', function () {
    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Harry Potter']);

    $books = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Books']);
    $films = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Films']);
    Item::factory()->create(['catalog_id' => $books->id, 'series_id' => $series->id, 'name' => 'Philosopher’s Stone']);
    Item::factory()->create(['catalog_id' => $films->id, 'series_id' => $series->id, 'name' => 'Chamber of Secrets (4K)']);

    $response = $this->actingAs($user)->get('/series/'.$series->id);

    $response->assertOk()
        ->assertSee('Harry Potter')
        ->assertSee('Books')
        ->assertSee('Films')
        ->assertSee('Philosopher’s Stone', false)
        ->assertSee('Chamber of Secrets (4K)');
});

it('does not show an item of the series that belongs to another account', function () {
    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->get('/series/'.$series->id)
        ->assertOk()
        ->assertSee('No items are linked to this series yet.', false);
});

it('returns not found for a series of another account', function () {
    $user = $this->createUser();
    $series = Series::factory()->create();

    $this->actingAs($user)->get('/series/'.$series->id)->assertNotFound();
});

it('creates a series', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/series', [
        'name' => 'Pink Floyd',
        'description' => 'Studio albums and tour memorabilia.',
    ]);

    $response->assertRedirect('/series')
        ->assertSessionHas('status', 'Series created');

    $series = Series::first();
    expect($series->name)->toBe('Pink Floyd');
    expect($series->account_id)->toBe($user->account_id);
});

it('rejects a series without a name', function () {
    Queue::fake();

    $user = $this->createUser();

    $this->actingAs($user)->post('/series', ['name' => ''])
        ->assertSessionHasErrors('name');

    expect(Series::count())->toBe(0);
});

it('updates a series', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Harry Poter']);

    $response = $this->actingAs($user)->put('/series/'.$series->id, [
        'name' => 'Harry Potter',
        'description' => 'Fixed the typo.',
    ]);

    $response->assertRedirect('/series')
        ->assertSessionHas('status', 'Series updated');

    expect($series->refresh()->name)->toBe('Harry Potter');
});

it('returns not found when updating a series of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create(['name' => 'Not yours']);

    $this->actingAs($user)->put('/series/'.$series->id, ['name' => 'Mine now'])->assertNotFound();

    expect($series->refresh()->name)->toBe('Not yours');
});

it('deletes a series and keeps its items', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id]);
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'series_id' => $series->id]);

    $response = $this->actingAs($user)->delete('/series/'.$series->id);

    $response->assertRedirect('/series')
        ->assertSessionHas('status', 'Series deleted');

    $this->assertModelMissing($series);
    $this->assertModelExists($item);
    expect($item->refresh()->series_id)->toBeNull();
});

it('returns not found when deleting a series of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create();

    $this->actingAs($user)->delete('/series/'.$series->id)->assertNotFound();

    $this->assertModelExists($series);
});

it('lets a viewer browse but not write', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $series = Series::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/series')->assertOk();
    $this->actingAs($viewer)->get('/series/'.$series->id)->assertOk();

    $this->actingAs($viewer)->post('/series', ['name' => 'Marvel'])->assertNotFound();
    $this->actingAs($viewer)->put('/series/'.$series->id, ['name' => 'Marvel'])->assertNotFound();
    $this->actingAs($viewer)->delete('/series/'.$series->id)->assertNotFound();
});
