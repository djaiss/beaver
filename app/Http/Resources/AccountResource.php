<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Account;
use App\Services\AccountPlan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Account
 */
class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $plan = new AccountPlan(account: $this->resource);

        return [
            'type' => 'account',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'currency_code' => $this->currency_code,
                'items_used' => $plan->itemsUsed(),
                // Null on a self hosted instance and on an unlocked account: there is
                // no ceiling to report, rather than a very large one.
                'item_limit' => $plan->isLimited() ? $plan->hardLimit() : null,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.account'),
            ],
        ];
    }
}
