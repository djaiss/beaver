<?php

declare(strict_types = 1);

namespace Tests\Feature\Console\Commands;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BrunoCommandTest extends TestCase
{
    #[Test]
    public function it_refreshes_and_seeds_the_database_then_updates_the_bruno_api_key(): void
    {
        $collectionPath = base_path('docs/LifeOS/collection.bru');
        $originalCollection = file_get_contents($collectionPath);

        $this->assertIsString($originalCollection);

        try {
            $this->artisan('lifeos:bruno')
                ->assertSuccessful();

            $user = User::query()
                ->where('email', 'admin@admin.com')
                ->first();

            $this->assertNotNull($user);
            $this->assertCount(1, $user->tokens);
            $this->assertSame('Bruno', $user->tokens->first()->name);

            $updatedCollection = file_get_contents($collectionPath);

            $this->assertIsString($updatedCollection);
            $this->assertMatchesRegularExpression(
                '/auth:bearer\s*\{\s*token: (?<id>\d+)\|(?<token>[A-Za-z0-9]+)/',
                $updatedCollection,
            );

            preg_match(
                '/auth:bearer\s*\{\s*token: \d+\|(?<token>[A-Za-z0-9]+)/',
                $updatedCollection,
                $matches,
            );

            $this->assertSame(hash('sha256', $matches['token']), $user->tokens->first()->token);
        } finally {
            file_put_contents($collectionPath, $originalCollection);
        }
    }
}
