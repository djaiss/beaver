<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCondition;
use App\Actions\DestroyCondition;
use App\Actions\UpdateCondition;
use App\Http\Controllers\Controller;
use App\Models\Condition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConditionController extends Controller
{
    public function index(Request $request): View
    {
        $conditions = $request->user()->account->conditions()
            ->get()
            ->sortBy(fn (Condition $condition): string => mb_strtolower($condition->name))
            ->values();

        return view('app.conditions.index', [
            'conditions' => $conditions,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new CreateCondition(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return to_route('settings.conditions.index')
            ->with('status', __('Condition created'))
            ->with('status_description', __('The condition can now be applied to items.'));
    }

    public function update(Request $request, int $condition): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $conditionModel = $account->conditions()->findOrFail($condition);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new UpdateCondition(
            user: $request->user(),
            condition: $conditionModel,
            name: $validated['name'],
        )->execute();

        return to_route('settings.conditions.index')
            ->with('status', __('Condition updated'))
            ->with('status_description', __('Your changes to the condition were saved.'));
    }

    public function destroy(Request $request, int $condition): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $conditionModel = $account->conditions()->findOrFail($condition);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyCondition(
            user: $request->user(),
            condition: $conditionModel,
        )->execute();

        return to_route('settings.conditions.index')
            ->with('status', __('Condition deleted'))
            ->with('status_description', __('The condition was removed from the account.'));
    }
}
