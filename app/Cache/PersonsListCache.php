<?php

declare(strict_types=1);

namespace App\Cache;

use App\Helpers\CacheHelper;
use App\Models\Person;
use App\Traits\CacheIdentifier;
use Illuminate\Support\Collection;

/**
 * Cache all the persons for the given account.
 */
final class PersonsListCache extends CacheHelper
{
    use CacheIdentifier;

    protected string $key = 'account.people-list:%s';

    protected int $ttl = 604800; // 1 week

    public function __construct(
        int $vaultId,
    ) {
        $this->identifier = $vaultId;
    }

    public static function make(int $vaultId): static
    {
        return new self($vaultId);
    }

    protected function generate(): Collection
    {
        return Person::query()->where('vault_id', $this->identifier)
            ->where('is_listed', true)
            ->select(
                'id',
                'first_name',
                'last_name',
                'maiden_name',
                'nickname',
                'slug',
            )
            ->get()
            ->map(fn (Person $person): array => [
                'id' => $person->id,
                'name' => $person->name,
                'maiden_name' => $person->maiden_name,
                'nickname' => $person->nickname,
                'slug' => $person->slug,
            ])
            ->sortBy('name');
    }
}
