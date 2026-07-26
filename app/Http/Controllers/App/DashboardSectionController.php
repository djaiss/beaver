<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\UpdateUserDashboardSections;
use App\Enums\DashboardSectionEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class DashboardSectionController extends Controller
{
    public function update(Request $request): Response
    {
        $validated = $request->validate([
            'sections' => ['present', 'array'],
            'sections.*' => [Rule::in(DashboardSectionEnum::values())],
        ]);

        new UpdateUserDashboardSections(
            user: $request->user(),
            hidden: array_values($validated['sections']),
        )->execute();

        return response()->noContent();
    }
}
