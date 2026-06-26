<?php

declare(strict_types=1);

namespace App\Traits;

trait CacheIdentifier
{
    protected int|string|null $identifier = null;

    public function getKey(): string
    {
        return str_replace('%s', (string) $this->identifier, $this->key);
    }
}
