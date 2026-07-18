<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCategory;
use App\Actions\DestroyCategory;
use App\Actions\UpdateCategory;
use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection as CollectionModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection): View
    {
        $collectionModel = $this->findCollection($request, $collection);

        $categories = $collectionModel->categories()->withCount('items')->get();

        return view('app.categories.index', [
            'collection' => $collectionModel,
            'tree' => $this->buildTree($categories),
            'parentOptions' => ['' => __('No parent (top level)')] + $categories->sortBy('name')->pluck('name', 'id')->all(),
            'totalCount' => $categories->count(),
            'topLevelCount' => $categories->whereNull('parent_id')->count(),
        ]);
    }

    public function create(Request $request, int $collection): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        new CreateCategory(
            user: $request->user(),
            collection: $collectionModel,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
        )->execute();

        return to_route('categories.index', $collectionModel->id)
            ->with('status', __('Category created'))
            ->with('status_description', __('Items in this collection can now be filed under it.'));
    }

    public function update(Request $request, int $collection, int $category): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $categoryModel = $this->findCategory($collectionModel, $category);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        new UpdateCategory(
            user: $request->user(),
            category: $categoryModel,
            name: $validated['name'],
            parentId: isset($validated['parent_id']) ? (int) $validated['parent_id'] : null,
        )->execute();

        return to_route('categories.index', $collectionModel->id)
            ->with('status', __('Category updated'))
            ->with('status_description', __('Your changes to the category were saved.'));
    }

    public function destroy(Request $request, int $collection, int $category): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $categoryModel = $this->findCategory($collectionModel, $category);

        new DestroyCategory(
            user: $request->user(),
            category: $categoryModel,
        )->execute();

        return to_route('categories.index', $collectionModel->id)
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
