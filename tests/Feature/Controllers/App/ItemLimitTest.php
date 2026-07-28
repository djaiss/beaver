<?php

declare(strict_types=1);

use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\CatalogView;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * What the collection screen and the add item route do as an account fills up.
 * The banner and the disabled button are the only places the free plan shows up
 * outside the upgrade screens, so they are covered together here.
 */
function editorWithItems(int $items, bool $hosted = true): User
{
    config(['pricing.hosted' => $hosted]);

    $account = test()->createAccount();
    $user = test()->createUser();
    test()->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Editor->value);

    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'name' => 'My Comics']);
    Item::factory()->count($items)->create(['catalog_id' => $catalog->id]);

    return $user->refresh();
}

function catalogOf(User $user): Catalog
{
    return $user->account->catalogs()->firstOrFail();
}

it('shows no banner while the account is inside the free plan', function () {
    $editor = editorWithItems(8);

    $this->actingAs($editor)
        ->get(route('collections.show', catalogOf($editor)))
        ->assertOk()
        ->assertDontSee('Upgrade account')
        ->assertSee(route('items.new', catalogOf($editor)));
});

it('shows the banner but keeps the add item link inside the grace', function () {
    $editor = editorWithItems(12);

    $this->actingAs($editor)
        ->get(route('collections.show', catalogOf($editor)))
        ->assertOk()
        ->assertSee('Upgrade account')
        ->assertSee('You are 2 items over the free plan.')
        ->assertSee('You can still add 3 more items before the account stops growing.')
        ->assertSee(route('items.new', catalogOf($editor)));
});

it('disables the add item button once the account is full', function () {
    $editor = editorWithItems(15);
    $catalog = catalogOf($editor);

    $this->actingAs($editor)
        ->get(route('collections.show', $catalog))
        ->assertOk()
        ->assertSee('This account is full. Adding items is paused.')
        ->assertSee('Upgrade account')
        ->assertDontSee(route('items.new', $catalog));
});

it('disables the add item button in the table view too', function () {
    $editor = editorWithItems(15);
    $catalog = catalogOf($editor);

    CatalogView::query()->create([
        'catalog_id' => $catalog->id,
        'user_id' => $editor->id,
        'view' => ItemViewEnum::Table,
    ]);

    $this->actingAs($editor)
        ->get(route('collections.show', $catalog))
        ->assertOk()
        ->assertSee('This account is full. Adding items is paused.')
        ->assertDontSee(route('items.new', $catalog));
});

it('sends the new item form to the upgrade screen once the account is full', function () {
    $editor = editorWithItems(15);

    $this->actingAs($editor)
        ->get(route('items.new', catalogOf($editor)))
        ->assertRedirect(route('upgrade.index'));
});

it('sends a direct submission to the upgrade screen once the account is full', function () {
    $editor = editorWithItems(15);

    $this->actingAs($editor)
        ->post(route('items.create', catalogOf($editor)), ['name' => 'Amazing Spider-Man #16'])
        ->assertRedirect(route('upgrade.index'));

    expect($editor->account->items()->count())->toBe(15);
});

it('leaves a self hosted instance alone however many items it holds', function () {
    $editor = editorWithItems(30, hosted: false);
    $catalog = catalogOf($editor);

    $this->actingAs($editor)
        ->get(route('collections.show', $catalog))
        ->assertOk()
        ->assertDontSee('Upgrade account')
        ->assertSee(route('items.new', $catalog));

    $this->actingAs($editor)->get(route('items.new', $catalog))->assertOk();
});
