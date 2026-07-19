<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateValuation;
use App\Actions\DestroyValuation;
use App\Actions\UpdateValuation;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The valuations of a copy, recorded from the history tab of its item.
 *
 * The amount is typed in currency units on the form and stored in cents, so the
 * controller is the only place that knows about the conversion.
 */
class ValuationController extends Controller
{
    use FindsItems;

    public function create(Request $request, int $collection, int $item, int $copy): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);

        $validated = $request->validate($this->rules());

        new CreateValuation(
            user: $request->user(),
            copy: $copyModel,
            type: ValuationType::from($validated['type']),
            amount: $this->toCents($validated['amount']),
            valuedAt: $validated['valued_at'],
            currencyCode: $validated['currency'] ?? null,
            confidence: ValuationConfidence::from($validated['confidence']),
            valuer: $validated['valuer'] ?? null,
            method: $validated['method'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'section' => 'valuations'])
            ->with('status', __('Valuation recorded'))
            ->with('status_description', __('The valuation was added to the history of this copy.'));
    }

    public function update(Request $request, int $collection, int $item, int $copy, int $valuation): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $valuationModel = $this->findValuation($copyModel, $valuation);

        $validated = $request->validate($this->rules());

        new UpdateValuation(
            user: $request->user(),
            valuation: $valuationModel,
            type: ValuationType::from($validated['type']),
            amount: $this->toCents($validated['amount']),
            valuedAt: $validated['valued_at'],
            currencyCode: $validated['currency'] ?? null,
            confidence: ValuationConfidence::from($validated['confidence']),
            valuer: $validated['valuer'] ?? null,
            method: $validated['method'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'section' => 'valuations'])
            ->with('status', __('Valuation updated'))
            ->with('status_description', __('Your changes to the valuation were saved.'));
    }

    public function destroy(Request $request, int $collection, int $item, int $copy, int $valuation): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $valuationModel = $this->findValuation($copyModel, $valuation);

        new DestroyValuation(
            user: $request->user(),
            valuation: $valuationModel,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'section' => 'valuations'])
            ->with('status', __('Valuation deleted'))
            ->with('status_description', __('The valuation was removed from the history of this copy.'));
    }

    private function findCopy(Item $item, int $copy): Copy
    {
        try {
            return $item->copies()->findOrFail($copy);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function findValuation(Copy $copy, int $valuation): Valuation
    {
        try {
            return $copy->valuations()->findOrFail($valuation);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ValuationType::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'valued_at' => ['required', 'date'],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'confidence' => ['required', Rule::enum(ValuationConfidence::class)],
            'valuer' => ['nullable', 'string', 'max:255'],
            'method' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2000'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * The form collects money in currency units, and everything is stored in
     * cents.
     */
    private function toCents(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }
}
