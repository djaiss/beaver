<?php

declare(strict_types=1);
use App\Enums\ItemViewEnum;
use App\Enums\VisibilityEnum;
use App\Models\Account;
use App\Models\Catalog;
use App\Models\CatalogView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('defaults the view for a user to the grid when none is stored', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    expect($catalog->viewForUser($user))->toBe(ItemViewEnum::Grid);
});

it('returns the stored view for a user', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    CatalogView::factory()->create([
        'user_id' => $user->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);

    expect($catalog->viewForUser($user))->toBe(ItemViewEnum::Table);
});

it('remembers the view per user', function () {
    $catalog = Catalog::factory()->create();
    $ross = $this->createUser();
    $rachel = $this->createUser();
    CatalogView::factory()->create([
        'user_id' => $ross->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);

    expect($catalog->viewForUser($ross))->toBe(ItemViewEnum::Table);
    expect($catalog->viewForUser($rachel))->toBe(ItemViewEnum::Grid);
});

it('belongs to an account', function () {
    $account = $this->createAccount();
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    expect($catalog->account)->toBeInstanceOf(Account::class);
    expect($catalog->account->id)->toBe($account->id);
});

it('generates a uuid on creation when none is given', function () {
    $account = $this->createAccount();

    $catalog = Catalog::query()->create([
        'account_id' => $account->id,
        'name' => 'Marvel Comics 1990s',
    ]);

    expect($catalog->uuid)->toBeString();
    expect($catalog->uuid)->not->toBeEmpty();
});

it('encrypts the name at rest', function () {
    $catalog = Catalog::factory()->create(['name' => 'Marvel Comics 1990s']);

    $rawName = DB::table('catalogs')->where('id', $catalog->id)->value('name');

    $this->assertNotSame('Marvel Comics 1990s', $rawName);
    expect($catalog->fresh()->name)->toBe('Marvel Comics 1990s');
});

it('casts the visibility to an enum', function () {
    $catalog = Catalog::factory()->create(['visibility' => VisibilityEnum::Public->value]);

    expect($catalog->fresh()->visibility)->toBe(VisibilityEnum::Public);
});

it('casts the settings to an array', function () {
    $catalog = Catalog::factory()->create(['settings' => ['theme' => 'dark']]);

    expect($catalog->fresh()->settings)->toBe(['theme' => 'dark']);
});

it('soft deletes', function () {
    $catalog = Catalog::factory()->create();

    $catalog->delete();

    $this->assertSoftDeleted($catalog);
    expect(Catalog::query()->find($catalog->id))->toBeNull();
    expect(Catalog::withTrashed()->find($catalog->id))->not->toBeNull();
});
