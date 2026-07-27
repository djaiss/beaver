<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\CloudflareCache;
use Illuminate\Console\Command;

/**
 * Drops every public page Cloudflare holds. Run it after changing anything the
 * public site shows that the application itself did not change, such as the
 * documentation portal, or after a deploy that alters a page.
 */
class PurgeCloudflareCacheCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'kollek:purge-cloudflare-cache';

    /**
     * @var string
     */
    protected $description = 'Purge everything Cloudflare holds for the public site';

    public function handle(): int
    {
        if (! CloudflareCache::isConfigured()) {
            $this->error('Cloudflare is not configured. Set CLOUDFLARE_API_TOKEN and CLOUDFLARE_ZONE_ID.');

            return self::FAILURE;
        }

        $this->line('Purging the Cloudflare cache…');

        if (! CloudflareCache::purgeEverything()) {
            $this->error('Cloudflare refused the purge. Check that the token carries the Cache Purge permission for that zone.');

            return self::FAILURE;
        }

        $this->info('Cloudflare cache purged. Every public page is rendered again on the next visit.');

        return self::SUCCESS;
    }
}
