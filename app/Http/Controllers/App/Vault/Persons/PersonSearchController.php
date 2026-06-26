<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Persons;

use App\Cache\PersonsListCache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PersonSearchController extends Controller
{
    /**
     * Search for a person based on a given term.
     *
     * @param  Request  $request  The request object.
     * @return View The view to render.
     */
    public function create(Request $request): View
    {
        $vault = $request->attributes->get('vault');

        $validated = $request->validate([
            'term' => ['required', 'string', 'max:255'],
        ]);

        $persons = PersonsListCache::make(
            vaultId: $vault->id,
        )->value();

        $filteredPersons = $persons->filter(function (array $personItem) use ($validated): bool {
            if ($validated['term'] === '' || $validated['term'] === '0') {
                return true;
            }
            if (Str::contains(
                mb_strtolower((string) $personItem['name']),
                mb_strtolower((string) $validated['term']),
            )) {
                return true;
            }
            if (Str::contains(
                mb_strtolower((string) $personItem['nickname']),
                mb_strtolower((string) $validated['term']),
            )) {
                return true;
            }

            return Str::contains(
                mb_strtolower((string) $personItem['maiden_name']),
                mb_strtolower((string) $validated['term']),
            );
        });

        return view('app.vault.person._list', [
            'vault' => $vault,
            'persons' => $filteredPersons,
            'person' => $request->attributes->get('person'),
        ]);
    }
}
