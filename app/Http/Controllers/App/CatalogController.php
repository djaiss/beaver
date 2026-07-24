<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCatalog;
use App\Actions\DestroyCatalog;
use App\Actions\UpdateCatalog;
use App\Enums\VisibilityEnum;
use App\Http\Controllers\Controller;
use App\Traits\ShowsCatalogItems;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CatalogController extends Controller
{
    use ShowsCatalogItems;

    /** @var list<string> */
    private const array EMOJI_OPTIONS = ['📦', '📚', '💿', '🃏', '🍷', '🎮', '🧸', '🪙', '🖼️', '⌚', '👟', '📷'];

    public function index(Request $request): View
    {
        $account = $request->user()->account;

        return view('app.catalogs.index', [
            'catalogs' => $account->catalogs()->with('createdBy')->orderByDesc('updated_at')->get(),
        ]);
    }

    public function show(Request $request): View
    {
        $catalog = $request->attributes->get('catalog');
        $catalog->load(['catalogTypes', 'categories']);

        return $this->catalogItemsView($request, $catalog);
    }

    public function new(Request $request): View
    {
        $account = $request->user()->account;

        return view('app.catalogs.new', [
            'types' => $account->catalogTypes()->orderBy('name')->get(),
            'currencies' => $this->currencyOptions(),
            'defaultCurrency' => $account->currency_code,
            'emojiOptions' => self::EMOJI_OPTIONS,
            'visibilityOptions' => $this->visibilityOptions(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $account = $request->user()->account;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
            'visibility' => ['required', Rule::enum(VisibilityEnum::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'collection_type_ids' => ['array'],
            'collection_type_ids.*' => ['integer'],
        ]);

        new CreateCatalog(
            user: $request->user(),
            account: $account,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
            catalogTypeIds: $validated['collection_type_ids'] ?? [],
        )->execute();

        return to_route('collections.index')
            ->with('status', __('Collection created'))
            ->with('status_description', __('Your new collection is ready for items.'));
    }

    public function edit(Request $request): View
    {
        $account = $request->user()->account;
        $catalog = $request->attributes->get('catalog');

        return view('app.catalogs.edit', [
            'types' => $account->catalogTypes()->get()->sortBy('name')->values(),
            'selectedTypeIds' => $catalog->catalogTypes->pluck('id')->all(),
            'currencies' => $this->currencyOptions(),
            'emojiOptions' => self::EMOJI_OPTIONS,
            'visibilityOptions' => $this->visibilityOptions(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $catalog = $request->attributes->get('catalog');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'emoji' => ['nullable', 'string', Rule::in(self::EMOJI_OPTIONS)],
            'visibility' => ['required', Rule::enum(VisibilityEnum::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'collection_type_ids' => ['array'],
            'collection_type_ids.*' => ['integer'],
        ]);

        new UpdateCatalog(
            user: $request->user(),
            catalog: $catalog,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            emoji: $validated['emoji'] ?? null,
            visibility: $validated['visibility'],
            currency: $validated['currency'] ?? null,
            catalogTypeIds: $validated['collection_type_ids'] ?? [],
        )->execute();

        return to_route('collections.show', $catalog->id)
            ->with('status', __('Collection updated'))
            ->with('status_description', __('Your changes to the collection were saved.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        new DestroyCatalog(
            user: $request->user(),
            catalog: $request->attributes->get('catalog'),
        )->execute();

        return to_route('collections.index')
            ->with('status', __('Collection deleted'))
            ->with('status_description', __('The collection and its items are no longer accessible.'));
    }

    /**
     * @return array<string, string>
     */
    private function currencyOptions(): array
    {
        return collect(config('currencies'))
            ->map(fn (array $currency, string $code): string => $currency['flag'].' '.$code)
            ->all();
    }

    /**
     * @return list<array{key: string, label: string, description: string}>
     */
    private function visibilityOptions(): array
    {
        return [
            ['key' => VisibilityEnum::Private->value, 'label' => __('Private'), 'description' => __('Only you can see this collection.')],
            ['key' => VisibilityEnum::Shared->value, 'label' => __('Shared'), 'description' => __('Visible to everyone in your account.')],
            ['key' => VisibilityEnum::Public->value, 'label' => __('Public'), 'description' => __('Anyone with the link can view it, read-only.')],
        ];
    }
}
