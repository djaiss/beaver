<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\UpdateVault;
use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandController extends Controller
{
    public function index(Request $request): View
    {
        $vault = $request->attributes->get('vault');

        $genders = Gender::query()->where('vault_id', $vault->id)
            ->orderBy('position')
            ->get()
            ->map(fn (Gender $gender) => (object) [
                'id' => $gender->id,
                'name' => $gender->name,
                'position' => $gender->position,
            ]);

        $relationshipTypeCategories = $vault->relationshipTypeCategories()
            ->with(['relationshipTypes' => function ($query): void {
                $query->orderBy('position');
            }])
            ->orderBy('position')
            ->get();

        return view('app.vault.adminland.index', [
            'genders' => $genders,
            'relationshipTypeCategories' => $relationshipTypeCategories,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vault_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
        ]);

        new UpdateVault(
            user: $request->user(),
            vault: $request->attributes->get('vault'),
            name: $validated['vault_name'],
        )->execute();

        return to_route('vault.adminland.index', $request->attributes->get('vault')->id)
            ->with('status', __('app/shared.changes_saved'));
    }
}
