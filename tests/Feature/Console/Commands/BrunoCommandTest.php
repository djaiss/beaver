<?php

declare(strict_types=1);
use App\Models\User;

it('refreshes and seeds the database then updates the bruno api key', function () {
    $collectionPath = base_path('docs/beaver/collection.bru');
    $originalCollection = file_get_contents($collectionPath);

    expect($originalCollection)->toBeString();

    try {
        $this->artisan('beaver:bruno')
            ->assertSuccessful();

        $user = User::query()
            ->where('email', 'admin@admin.com')
            ->first();

        expect($user)->not->toBeNull();
        expect($user->tokens)->toHaveCount(1);
        expect($user->tokens->first()->name)->toBe('Bruno');

        $updatedCollection = file_get_contents($collectionPath);

        expect($updatedCollection)->toBeString();
        expect($updatedCollection)->toMatch('/auth:bearer\s*\{\s*token: (?<id>\d+)\|(?<token>[A-Za-z0-9]+)/');

        preg_match(
            '/auth:bearer\s*\{\s*token: \d+\|(?<token>[A-Za-z0-9]+)/',
            $updatedCollection,
            $matches,
        );

        expect($user->tokens->first()->token)->toBe(hash('sha256', $matches['token']));
    } finally {
        file_put_contents($collectionPath, $originalCollection);
    }
});
