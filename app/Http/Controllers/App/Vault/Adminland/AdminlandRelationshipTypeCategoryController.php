<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\CreateRelationshipTypeCategory;
use App\Actions\DestroyRelationshipTypeCategory;
use App\Actions\UpdateRelationshipTypeCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandRelationshipTypeCategoryController extends Controller
{
    public function new(Request $request): View
    {
        return view('app.vault.adminland._relationship-type-category-new', [
            'vault' => $request->attributes->get('vault'),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        new CreateRelationshipTypeCategory(
            user: $request->user(),
            vault: $vault,
            key: null,
            name: $validated['name'],
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('Changes saved'));
    }

    public function edit(Request $request): View
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()
            ->findOrFail($id);

        return view('app.vault.adminland._relationship-type-category-edit', [
            'relationshipTypeCategory' => $relationshipTypeCategory,
            'vault' => $vault,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        new UpdateRelationshipTypeCategory(
            user: $request->user(),
            relationshipTypeCategory: $relationshipTypeCategory,
            name: $validated['name'],
            position: $relationshipTypeCategory->position,
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('Changes saved'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()
            ->findOrFail($id);

        new DestroyRelationshipTypeCategory(
            user: $request->user(),
            relationshipTypeCategory: $relationshipTypeCategory,
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('Changes saved'));
    }
}
