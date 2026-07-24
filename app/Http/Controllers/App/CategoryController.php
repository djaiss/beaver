<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCategory;
use App\Actions\DestroyCategory;
use App\Actions\UpdateCategory;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection as CollectionModel;
use App\Traits\ShowsCollectionItems;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use ShowsCollectionItems;

    public function index(Request $request): View
    {
        $categories = $request->attributes->get('collection')->categories()->withCount('items')->get();

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
    public function show(Request $request, CollectionModel $collection, int $category): View
    {
        $collection->load(['collectionTypes', 'categories']);

        return $this->collectionItemsView($request, $collection, $this->findCategory($collection, $category));
    }

    public function create(Request $request): RedirectResponse
    {
        $collection = $request->attributes->get('collection');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        new CreateCategory(
            user: $request->user(),
            collection: $collection,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
            description: $validated['description'] ?? null,
        )->execute();

        return to_route('categories.index', $collection->id)
            ->with('status', __('Category created'))
            ->with('status_description', __('Items in this collection can now be filed under it.'));
    }

    public function update(Request $request, CollectionModel $collection, int $category): RedirectResponse
    {
        $categoryModel = $this->findCategory($collection, $category);

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

        return to_route('categories.index', $collection->id)
            ->with('status', __('Category updated'))
            ->with('status_description', __('Your changes to the category were saved.'));
    }

    public function destroy(Request $request, CollectionModel $collection, int $category): RedirectResponse
    {
        new DestroyCategory(
            user: $request->user(),
            category: $this->findCategory($collection, $category),
        )->execute();

        return to_route('categories.index', $collection->id)
            ->with('status', __('Category deleted'))
            ->with('status_description', __('Items that were filed under it keep their data.'));
    }

    private function findCategory(CollectionModel $collection, int $category): Category
    {
        try {
            return $collection->categories()->findOrFail($category);
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
