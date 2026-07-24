<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\UpdateCatalogItemView;
use App\Enums\ItemViewEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CatalogItemViewController extends Controller
{
    public function update(Request $request): Response
    {
        $validated = $request->validate([
            'view' => ['required', Rule::in(ItemViewEnum::values())],
        ]);

        new UpdateCatalogItemView(
            user: $request->user(),
            catalog: $request->attributes->get('catalog'),
            view: $validated['view'],
        )->execute();

        return response()->noContent();
    }
}
