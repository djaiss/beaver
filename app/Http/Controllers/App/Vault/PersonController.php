<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault;

use App\Actions\CreatePerson;
use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function new(Request $request): View
    {
        $vault = $request->attributes->get('vault');

        $genders = $vault->genders()
            ->orderBy('position')
            ->get()
            ->mapWithKeys(fn (Gender $gender): array => [$gender->id => $gender->name]);

        return view('app.vault.person.create', [
            'genders' => $genders,
            'vault' => $vault,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');

        $validated = $request->validate([
            'gender_id' => [
                'nullable',
                'integer',
                Rule::exists(Gender::class, 'id')
                    ->where(fn (Builder $query): Builder => $query->where('vault_id', $vault->id)),
            ],
            'kids_status' => ['nullable', Rule::in(['no_kids', 'maybe_kids', 'has_kids'])],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'maiden_name' => ['nullable', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:100'],
            'prefix' => ['nullable', 'string', 'max:100'],
        ]);

        $gender = isset($validated['gender_id'])
            ? $vault->genders()->findOrFail($validated['gender_id'])
            : null;
        new CreatePerson(
            user: $request->user(),
            vault: $vault,
            gender: $gender,
            firstName: $validated['first_name'],
            middleName: $validated['middle_name'] ?? null,
            lastName: $validated['last_name'] ?? null,
            nickname: $validated['nickname'] ?? null,
            maidenName: $validated['maiden_name'] ?? null,
            suffix: $validated['suffix'] ?? null,
            prefix: $validated['prefix'] ?? null,
            kidsStatus: $validated['kids_status'] ?? null,
        )->execute();

        return to_route('vault.person.index', $vault->id)
            ->with('status', __('app/person.new.created'));
    }
}
