<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCategory;
use App\Actions\DestroyCategory;
use App\Actions\UpdateCategory;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Category;
use App\Traits\ShowsCatalogItems;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use ShowsCatalogItems;

    public function index(Request $request): View
    {
        $categories = $request->attributes->get('catalog')->categories()->withCount('items')->get();

        return view('app.categories.index', [
            'tree' => $this->buildTree($categories),
            'parentOptions' => ['' => __('No parent (top level)')] + $categories->sortBy('name')->pluck('name', 'id')->all(),
            'totalCount' => $categories->count(),
            'topLevelCount' => $categories->whereNull('parent_id')->count(),
        ]);
    }

    /**
     * The items of the collection, narrowed down to the ones filed under this
     * category. It is the collection screen, over a smaller set of items.
     */
    public function show(Request $request, Catalog $catalog, int $category): View
    {
        $catalog->load(['catalogTypes', 'categories']);

        return $this->catalogItemsView($request, $catalog, $this->findCategory($catalog, $category));
    }

    public function create(Request $request): RedirectResponse
    {
        $catalog = $request->attributes->get('catalog');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        new CreateCategory(
            user: $request->user(),
            catalog: $catalog,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
            description: $validated['description'] ?? null,
        )->execute();

        return to_route('categories.index', $catalog->id)
            ->with('status', __('Category created'))
            ->with('status_description', __('Items in this collection can now be filed under it.'));
    }

    public function update(Request $request, Catalog $catalog, int $category): RedirectResponse
    {
        $categoryModel = $this->findCategory($catalog, $category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        new UpdateCategory(
            user: $request->user(),
            category: $categoryModel,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
            description: $validated['description'] ?? null,
        )->execute();

        return to_route('categories.index', $catalog->id)
            ->with('status', __('Category updated'))
            ->with('status_description', __('Your changes to the category were saved.'));
    }

    public function destroy(Request $request, Catalog $catalog, int $category): RedirectResponse
    {
        new DestroyCategory(
            user: $request->user(),
            category: $this->findCategory($catalog, $category),
        )->execute();

        return to_route('categories.index', $catalog->id)
            ->with('status', __('Category deleted'))
            ->with('status_description', __('Items that were filed under it keep their data.'));
    }

    private function findCategory(Catalog $catalog, int $category): Category
    {
        try {
            return $catalog->categories()->findOrFail($category);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * Group the flat list of categories into a nested tree, sorted by name
     * within each level. Names are encrypted, so the sort happens in memory.
     *
     * @param  Collection<int, Category>  $categories
     * @return list<array{category: Category, children: array<mixed>}>
     */
    private function buildTree(Collection $categories, ?int $parentId = null): array
    {
        return $categories
            ->where('parent_id', $parentId)
            ->sortBy('name')
            ->map(fn (Category $category): array => [
                'category' => $category,
                'children' => $this->buildTree($categories, $category->id),
            ])
            ->values()
            ->all();
    }
}
