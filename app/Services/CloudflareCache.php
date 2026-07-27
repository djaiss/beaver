<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * The public site is rendered by us and then held by Cloudflare for a week, so
 * anything that changes what a public page shows has to tell Cloudflare to drop
 * what it holds. That is what this does, and it is the only way a change reaches
 * a visitor before the week is out.
 *
 * An instance that does not sit behind Cloudflare, which is most self hosted
 * ones, configures nothing and every purge here quietly does nothing.
 */
class CloudflareCache
{
    public static function isConfigured(): bool
    {
        return config('services.cloudflare.api_token') !== null
            && config('services.cloudflare.zone_id') !== null;
    }

    /**
     * Drop every page Cloudflare holds for the zone. Returns whether the purge
     * actually happened, so a caller that reports back to a human can say so.
     */
    public static function purgeEverything(): bool
    {
        if (! self::isConfigured()) {
            return false;
        }

        $url = 'https://api.cloudflare.com/client/v4/zones/'.config('services.cloudflare.zone_id').'/purge_cache';

        try {
            $response = Http::withToken(config('services.cloudflare.api_token'))
                ->timeout(10)
                ->post($url, ['purge_everything' => true]);
        } catch (ConnectionException) {
            return false;
        }

        return $response->successful();
    }
}
