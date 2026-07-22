<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\ExportCollectionType;
use App\Http\Controllers\Controller;
use App\Models\CollectionType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectionTypeExportController extends Controller
{
    public function show(Request $request, int $collectionType): View
    {
        $type = $this->findType($request, $collectionType);

        $schema = new ExportCollectionType(
            user: $request->user(),
            collectionType: $type,
        )->execute();

        $json = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $lineCount = substr_count($json, "\n") + 1;

        return view('app.types.export', [
            'type' => $type,
            'json' => $json,
            'lineCount' => $lineCount,
            'gutter' => implode("\n", range(1, $lineCount)),
            'size' => $this->humanSize(strlen($json)),
            'fileName' => $this->fileName($type),
        ]);
    }

    private function findType(Request $request, int $collectionType): CollectionType
    {
        try {
            return $request->user()->account->collectionTypes()->findOrFail($collectionType);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * The name is free text, so it can slug down to nothing (emoji only, say).
     */
    private function fileName(CollectionType $type): string
    {
        $slug = Str::slug($type->name);

        return ($slug !== '' ? $slug : 'type').'.type.json';
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        return number_format($bytes / 1024, 1).' KB';
    }
}
