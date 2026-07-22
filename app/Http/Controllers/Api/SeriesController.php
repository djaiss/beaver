<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateSeries;
use App\Actions\DestroySeries;
use App\Actions\UpdateSeries;
use App\Http\Controllers\Controller;
use App\Http\Resources\SeriesResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SeriesController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $series = $account->series()
            ->orderBy('id')
            ->paginate($perPage);

        return SeriesResource::collection($series);
    }

    public function show(Request $request): JsonResponse
    {
        $seriesId = $request->route()->parameter('series');

        $series = $request->user()->account->series()->findOrFail($seriesId);

        return new SeriesResource($series)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $series = new CreateSeries(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            description: $validated['description'] ?? null,
        )->execute();

        return new SeriesResource($series)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $seriesId = $request->route()->parameter('series');

        $series = $request->user()->account->series()->findOrFail($seriesId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $series = new UpdateSeries(
            user: $request->user(),
            series: $series,
            name: $validated['name'],
            description: $validated['description'] ?? null,
        )->execute();

        return new SeriesResource($series)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $seriesId = $request->route()->parameter('series');

        $series = $request->user()->account->series()->findOrFail($seriesId);

        new DestroySeries(
            user: $request->user(),
            series: $series,
        )->execute();

        return response()->noContent(204);
    }
}
