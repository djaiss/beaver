<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\CreateRelationshipType;
use App\Actions\DestroyRelationshipType;
use App\Actions\UpdateRelationshipType;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandRelationshipTypeController extends Controller
{
    public function new(Request $request): View
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        return view('app.vault.adminland._relationship-type-new', [
            'relationshipTypeCategory' => $relationshipTypeCategory,
            'vault' => $vault,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'is_directed' => ['sometimes', 'boolean'],
            'forward_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
            'reverse_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
        ]);

        new CreateRelationshipType(
            user: $request->user(),
            vault: $vault,
            relationshipTypeCategory: $relationshipTypeCategory,
            key: null,
            name: $validated['name'],
            isDirected: (bool) ($validated['is_directed'] ?? false),
            forwardName: $validated['forward_name'] ?? null,
            reverseName: $validated['reverse_name'] ?? null,
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }

    public function edit(Request $request): View
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $relationshipType = $relationshipTypeCategory
            ->relationshipTypes()
            ->where('vault_id', $vault->id)
            ->findOrFail($request->route()->parameter('relationshipType'));

        return view('app.vault.adminland._relationship-type-edit', [
            'relationshipType' => $relationshipType,
            'relationshipTypeCategory' => $relationshipTypeCategory,
            'vault' => $vault,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $relationshipType = $relationshipTypeCategory
            ->relationshipTypes()
            ->where('vault_id', $vault->id)
            ->findOrFail($request->route()->parameter('relationshipType'));

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'is_directed' => ['sometimes', 'boolean'],
            'forward_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
            'reverse_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
        ]);

        new UpdateRelationshipType(
            user: $request->user(),
            relationshipType: $relationshipType,
            name: $validated['name'],
            isDirected: (bool) ($validated['is_directed'] ?? false),
            position: $relationshipType->position,
            forwardName: $validated['forward_name'] ?? null,
            reverseName: $validated['reverse_name'] ?? null,
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $relationshipType = $relationshipTypeCategory
            ->relationshipTypes()
            ->where('vault_id', $vault->id)
            ->findOrFail($request->route()->parameter('relationshipType'));

        new DestroyRelationshipType(
            user: $request->user(),
            relationshipType: $relationshipType,
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }
}
