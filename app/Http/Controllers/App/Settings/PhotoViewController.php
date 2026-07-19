<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\UpdateUserPhotoView;
use App\Enums\PhotoViewEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PhotoViewController extends Controller
{
    public function update(Request $request): Response
    {
        $validated = $request->validate([
            'view' => ['required', Rule::in(PhotoViewEnum::values())],
        ]);

        new UpdateUserPhotoView(
            user: $request->user(),
            view: $validated['view'],
        )->execute();

        return response()->noContent();
    }
}
