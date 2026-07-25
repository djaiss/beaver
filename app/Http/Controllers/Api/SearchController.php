<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\SearchableEnum;
use App\Http\Controllers\Controller;
use App\Services\AccountSearch;
use App\ValueObjects\SearchResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    /**
     * Search everything in the caller's account. Results are assembled at read
     * time rather than stored, so they are returned as one object instead of an
     * Eloquent resource.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:'.implode(',', array_column(SearchableEnum::cases(), 'value'))],
        ]);

        $search = new AccountSearch(
            account: $request->user()->account,
            user: $request->user(),
            query: $validated['q'],
            type: isset($validated['type']) ? SearchableEnum::from($validated['type']) : null,
        );

        return response()->json([
            'data' => [
                'type' => 'search_results',
                'attributes' => [
                    'query' => $search->query(),
                    'total' => $search->total(),
                    'matched' => $search->matched(),
                    'truncated' => $search->isCapped(),
                    'counts' => $search->countsByType(),
                    'results' => $this->results($search),
                ],
            ],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function results(AccountSearch $search): array
    {
        return $search->groups()
            ->flatMap(fn (array $group): Collection => $group['results'])
            ->map(fn (SearchResult $result): array => [
                'type' => $result->type->value,
                'id' => (string) $result->id,
                'title' => $result->title,
                'context' => $result->context,
                'collection_name' => $result->collectionName,
                'score' => $result->score,
                'name_match' => $result->isTitleMatch(),
                'links' => [
                    'self' => $result->url,
                ],
            ])
            ->all();
    }
}
