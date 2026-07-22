<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\ImportCollectionType;
use App\Enums\FieldTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionTypeImportController extends Controller
{
    public function new(): View
    {
        return view('app.types.import', [
            'maxLength' => ImportCollectionType::MAX_LENGTH,
            'fieldTypes' => array_column(FieldTypeEnum::cases(), 'value'),
            'sample' => $this->sample(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'json' => ['required', 'string', 'max:'.ImportCollectionType::MAX_LENGTH],
        ]);

        $type = new ImportCollectionType(
            user: $request->user(),
            account: $request->user()->account,
            json: $validated['json'],
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Type imported'))
            ->with('status_description', __('":name" is ready to use in a collection.', ['name' => $type->name]));
    }

    /**
     * A document the user can load into the editor to see what a valid import
     * looks like, in the exact shape ExportCollectionType hands out.
     */
    private function sample(): string
    {
        return json_encode([
            'schemaVersion' => 1,
            'type' => [
                'name' => 'Comics',
                'color' => '#FB923C',
                'groups' => [
                    [
                        'name' => 'Publishing info',
                        'fields' => [
                            ['name' => 'Issue #', 'type' => 'number'],
                            ['name' => 'Publisher', 'type' => 'text'],
                            ['name' => 'Cover date', 'type' => 'date'],
                        ],
                    ],
                    [
                        'name' => 'Condition and grading',
                        'fields' => [
                            ['name' => 'Grade', 'type' => 'select', 'options' => ['CGC 9.8', 'CGC 9.6', 'Raw']],
                            ['name' => 'Signed', 'type' => 'boolean'],
                        ],
                    ],
                ],
                'standaloneFields' => [],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
