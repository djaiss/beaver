<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\UpdateRelationshipType;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminlandRelationshipTypePositionController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');
        $relationshipTypeCategory = $vault->relationshipTypeCategories()
            ->findOrFail($id);

        $relationshipType = $relationshipTypeCategory->relationshipTypes()
            ->where('vault_id', $vault->id)
            ->findOrFail($request->route()->parameter('relationshipType'));

        $validated = $request->validate([
            'position' => ['required', 'integer', 'min:1'],
        ]);

        new UpdateRelationshipType(
            user: $request->user(),
            relationshipType: $relationshipType,
            name: $relationshipType->name,
            isDirected: $relationshipType->is_directed,
            position: (int) $validated['position'],
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('Changes saved'));
    }
}
