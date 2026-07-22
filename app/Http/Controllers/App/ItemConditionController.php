<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateItemCondition;
use App\Actions\DestroyItemCondition;
use App\Actions\UpdateItemCondition;
use App\Http\Controllers\Controller;
use App\Models\ItemCondition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemConditionController extends Controller
{
    public function index(Request $request): View
    {
        $conditions = $request->user()->account->itemConditions()
            ->get()
            ->sortBy(fn (ItemCondition $itemCondition): string => mb_strtolower($itemCondition->name))
            ->values();

        return view('app.item-conditions.index', [
            'conditions' => $conditions,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new CreateItemCondition(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return to_route('settings.itemConditions.index')
            ->with('status', __('Condition created'))
            ->with('status_description', __('The condition can now be applied to items.'));
    }

    public function update(Request $request, int $itemCondition): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $itemConditionModel = $account->itemConditions()->findOrFail($itemCondition);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new UpdateItemCondition(
            user: $request->user(),
            itemCondition: $itemConditionModel,
            name: $validated['name'],
        )->execute();

        return to_route('settings.itemConditions.index')
            ->with('status', __('Condition updated'))
            ->with('status_description', __('Your changes to the condition were saved.'));
    }

    public function destroy(Request $request, int $itemCondition): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $itemConditionModel = $account->itemConditions()->findOrFail($itemCondition);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyItemCondition(
            user: $request->user(),
            itemCondition: $itemConditionModel,
        )->execute();

        return to_route('settings.itemConditions.index')
            ->with('status', __('Condition deleted'))
            ->with('status_description', __('The condition was removed from the account.'));
    }
}
