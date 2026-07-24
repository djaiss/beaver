<?php

declare(strict_types=1);
use App\Models\Catalog;
use App\Models\SearchToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an account', function () {
    $catalog = Catalog::factory()->create();
    $token = SearchToken::create([
        'account_id' => $catalog->account_id,
        'searchable_type' => 'collection',
        'searchable_id' => $catalog->id,
        'token' => str_repeat('a', 64),
        'weight' => 100,
    ]);

    expect($token->account()->exists())->toBeTrue();
});

it('belongs to the record it makes searchable', function () {
    $catalog = Catalog::factory()->create();
    $token = SearchToken::create([
        'account_id' => $catalog->account_id,
        'searchable_type' => 'collection',
        'searchable_id' => $catalog->id,
        'token' => str_repeat('b', 64),
        'weight' => 100,
    ]);

    expect($token->searchable->is($catalog))->toBeTrue();
});
