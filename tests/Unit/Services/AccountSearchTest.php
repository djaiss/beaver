<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\SearchableEnum;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Tag;
use App\Services\AccountSearch;
use App\ValueObjects\SearchResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function titlesOf(AccountSearch $search): array
{
    return $search->groups()
        ->flatMap(fn (array $group): Collection => $group['results'])
        ->map(fn (SearchResult $result): string => $result->title)
        ->all();
}

it('finds an item by its name', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man #300']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'spider');

    expect(titlesOf($search))->toContain('Amazing Spider-Man #300');
});

it('finds a collection by its name', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Central Perk Mugs']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'perk');

    expect(titlesOf($search))->toContain('Central Perk Mugs');
});

it('reaches encrypted names through the index rather than a like clause', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Central Perk Mugs']);

    $stored = DB::table('catalogs')->where('account_id', $user->account_id)->value('name');
    expect($stored)->not->toContain('Central Perk');

    $search = new AccountSearch(account: $user->account, user: $user, query: 'perk');

    expect($search->total())->toBe(1);
});

it('requires every word of the query to match', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Fantasy']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'amazing spider');

    expect(titlesOf($search))->toBe(['Amazing Spider-Man']);
});

it('matches a word from its start', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider-Man']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'spi');

    expect(titlesOf($search))->toContain('Spider-Man');
});

it('ignores case and punctuation', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'ASM-300']);
    Copy::factory()->create(['item_id' => $item->id, 'identifier' => 'ASM-300-B']);

    foreach (['asm-300', 'asm 300', 'ASM_300'] as $query) {
        $search = new AccountSearch(account: $user->account, user: $user, query: $query);

        expect($search->total())->toBe(2, "query [{$query}]");
    }
});

it('returns nothing for a query made only of single letters', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'a');

    expect($search->total())->toBe(0);
    expect($search->isQueryTooShort())->toBeTrue();
});

it('returns nothing for an empty query', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    $search = new AccountSearch(account: $user->account, user: $user, query: '  ');

    expect($search->hasQuery())->toBeFalse();
    expect($search->total())->toBe(0);
    expect($search->isQueryTooShort())->toBeFalse();
});

it('never reaches into another account', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['name' => 'Central Perk Mugs']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'perk');

    expect($search->total())->toBe(0);
});

it('leaves a soft deleted record out', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Smelly Cat']);

    $item->delete();

    $search = new AccountSearch(account: $user->account, user: $user, query: 'smelly');

    expect($search->total())->toBe(0);
});

it('ranks a name match above a match on something filed around the record', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Spider']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Smelly Cat']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider Sense']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'spider');
    $results = $search->groups()->flatMap(fn (array $group) => $group['results']);

    expect($results->firstWhere('title', 'Spider Sense')->score)->toBe(100);
    expect($results->firstWhere('title', 'Smelly Cat')->score)->toBe(60);
});

it('narrows the results to one kind of record', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Spider']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider Sense']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'spider', type: SearchableEnum::Catalog);

    expect(titlesOf($search))->toBe(['Spider']);
    expect($search->total())->toBe(2);
    expect($search->matched())->toBe(1);
});

it('counts every match but shows at most fifty', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->count(60)->create(['catalog_id' => $catalog->id, 'name' => 'Spider Sense']);

    $search = new AccountSearch(account: $user->account, user: $user, query: 'spider');

    expect($search->total())->toBe(60);
    expect($search->isCapped())->toBeTrue();
    expect(count(titlesOf($search)))->toBe(50);
});

it('hides tags from a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $search = new AccountSearch(account: $account, user: $viewer, query: 'signed');

    expect($search->total())->toBe(0);
});

it('shows tags to an editor', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $search = new AccountSearch(account: $account, user: $editor, query: 'signed');

    expect(titlesOf($search))->toBe(['Signed']);
});

it('counts what the account has indexed', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    Item::factory()->count(2)->create(['catalog_id' => $catalog->id]);

    $search = new AccountSearch(account: $user->account, user: $user, query: '');

    expect($search->indexedCounts()['item'])->toBe(2);
    expect($search->indexedCounts()['collection'])->toBe(1);
});
