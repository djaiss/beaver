<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCollectionType;
use App\Actions\DestroyCollectionType;
use App\Actions\UpdateCollectionType;
use App\Enums\FieldTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionTypeController extends Controller
{
    /** @var list<string> */
    private const array PALETTE = ['#fb923c', '#8b5cf6', '#34d399', '#ec4899', '#3b82f6'];

    public function index(Request $request): View
    {
        $account = $request->user()->account;

        abort_unless($account->allowsManagementBy($request->user()), 404);

        $types = $account->collectionTypes()
            ->with('customFields')
            ->withCount('collections')
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (CollectionType $type): object => (object) [
                'id' => $type->id,
                'name' => $type->name,
                'color' => $type->color,
                'field_count' => $type->customFields->count(),
                'collection_count' => $type->collections_count,
                'field_summary' => $this->fieldSummary($type),
                'updated_at' => $type->updated_at?->diffForHumans(),
            ]);

        return view('app.types.index', [
            'types' => $types,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $type = new CreateCollectionType(
            user: $request->user(),
            account: $request->user()->account,
            name: __('New type'),
            color: self::PALETTE[0],
        )->execute();

        return to_route('types.edit', $type->id);
    }

    public function edit(Request $request, int $collectionType): View
    {
        $account = $request->user()->account;

        abort_unless($account->allowsManagementBy($request->user()), 404);

        try {
            $type = $account->collectionTypes()
                ->with(['customFields' => fn ($query) => $query->orderBy('position')->orderBy('id'), 'collections'])
                ->findOrFail($collectionType);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('app.types.edit', [
            'type' => $type,
            'collections' => $account->collections()->get(),
            'fieldTypes' => $this->fieldTypeOptions(),
            'palette' => self::PALETTE,
        ]);
    }

    public function update(Request $request, int $collectionType): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $type = $account->collectionTypes()->findOrFail($collectionType);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        new UpdateCollectionType(
            user: $request->user(),
            collectionType: $type,
            name: $validated['name'],
            color: $validated['color'],
        )->execute();

        return to_route('types.edit', $type->id)
            ->with('status', __('Type updated'));
    }

    public function destroy(Request $request, int $collectionType): RedirectResponse
    {
        $account = $request->user()->account;

        try {
            $type = $account->collectionTypes()->findOrFail($collectionType);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyCollectionType(
            user: $request->user(),
            collectionType: $type,
        )->execute();

        return to_route('types.index')
            ->with('status', __('Type deleted'));
    }

    private function fieldSummary(CollectionType $type): string
    {
        if ($type->customFields->isEmpty()) {
            return __('No custom fields');
        }

        $names = $type->customFields
            ->sortBy('position')
            ->take(4)
            ->map(fn (CustomField $field): string => $field->name !== '' ? $field->name : __('(untitled)'))
            ->implode(', ');

        return $type->customFields->count() > 4 ? $names.'…' : $names;
    }

    /**
     * @return array<string, string>
     */
    private function fieldTypeOptions(): array
    {
        return [
            FieldTypeEnum::Text->value => __('Text'),
            FieldTypeEnum::Number->value => __('Number'),
            FieldTypeEnum::Date->value => __('Date'),
            FieldTypeEnum::Boolean->value => __('Yes / No'),
            FieldTypeEnum::Select->value => __('Select'),
        ];
    }
}
