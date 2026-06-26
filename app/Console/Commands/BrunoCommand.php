<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use SensitiveParameter;

class BrunoCommand extends Command
{
    private const string ADMIN_EMAIL = 'admin@admin.com';

    private const string TOKEN_NAME = 'Bruno';

    /**
     * @var string
     */
    protected $signature = 'lifeos:bruno';

    /**
     * @var string
     */
    protected $description = 'Refresh and seed the database, then configure the Bruno collection API key';

    public function handle(): int
    {
        $collectionPath = base_path('docs/LifeOS/collection.bru');
        $collection = $this->readBrunoCollection($collectionPath);

        if ($collection === null) {
            return self::FAILURE;
        }

        if ($this->call('migrate:fresh', ['--seed' => true]) !== self::SUCCESS) {
            $this->error('The database could not be refreshed and seeded.');

            return self::FAILURE;
        }

        $user = User::query()
            ->where('email', self::ADMIN_EMAIL)
            ->first();

        if ($user === null) {
            $adminEmail = self::ADMIN_EMAIL;
            $this->error("The seeded user {$adminEmail} was not found.");

            return self::FAILURE;
        }

        $apiKey = $user->createToken(self::TOKEN_NAME)->plainTextToken;
        $updatedCollection = $this->replaceApiKey($collection, $apiKey);

        if ($updatedCollection === null || ! File::put($collectionPath, $updatedCollection)) {
            $this->error('The Bruno collection API key could not be updated.');

            return self::FAILURE;
        }

        $this->info('The database was refreshed and the Bruno collection API key was updated.');

        return self::SUCCESS;
    }

    private function readBrunoCollection(string $collectionPath): ?string
    {
        if (! File::isFile($collectionPath)) {
            $this->error("The Bruno collection was not found at {$collectionPath}.");

            return null;
        }

        $collection = File::get($collectionPath);

        if ($this->replaceApiKey($collection, 'token') === null) {
            $this->error('The Bruno collection does not contain a bearer token field.');

            return null;
        }

        return $collection;
    }

    private function replaceApiKey(string $collection, #[SensitiveParameter] string $apiKey): ?string
    {
        $replacementCount = 0;
        $updatedCollection = preg_replace_callback(
            '/(?<prefix>auth:bearer\s*\{\s*token:\s*)[^\r\n}]*/',
            static fn (array $matches): string => "{$matches['prefix']}{$apiKey}",
            $collection,
            1,
            $replacementCount,
        );

        if ($updatedCollection === null || $replacementCount !== 1) {
            return null;
        }

        return $updatedCollection;
    }
}
