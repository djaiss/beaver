<?php

declare(strict_types=1);
use App\Services\BlindIndex;

it('hashes the same term to the same value', function () {
    expect(BlindIndex::hash('monica'))->toBe(BlindIndex::hash('monica'));
});

it('hashes different terms to different values', function () {
    expect(BlindIndex::hash('monica'))->not->toBe(BlindIndex::hash('rachel'));
});

it('produces a hash that reveals nothing of the term', function () {
    $hash = BlindIndex::hash('monica');

    expect($hash)->toHaveLength(64);
    expect($hash)->not->toContain('monica');
});

it('splits a file name into its words', function () {
    $hashes = BlindIndex::hashesFor('kob_front_cover.jpg');

    expect($hashes)->toContain(BlindIndex::hash('front'));
    expect($hashes)->toContain(BlindIndex::hash('cover'));
    expect($hashes)->toContain(BlindIndex::hash('jpg'));
});

it('indexes every prefix of a word, so a partial query finds it', function () {
    $hashes = BlindIndex::hashesFor('Gunther.png');

    expect($hashes)->toContain(BlindIndex::hash('gu'));
    expect($hashes)->toContain(BlindIndex::hash('gunt'));
    expect($hashes)->toContain(BlindIndex::hash('gunther'));
});

it('ignores case', function () {
    expect(BlindIndex::hashesFor('MONICA'))->toBe(BlindIndex::hashesFor('monica'));
});

it('matches a query against the prefixes it indexed', function () {
    $hashes = BlindIndex::hashesFor('central_perk_sign.jpg', 'The Coffee House');

    foreach (BlindIndex::hashesForQuery('perk') as $hash) {
        expect($hashes)->toContain($hash);
    }

    foreach (BlindIndex::hashesForQuery('coff') as $hash) {
        expect($hashes)->toContain($hash);
    }
});

it('returns one hash per word of a query', function () {
    expect(BlindIndex::hashesForQuery('coffee house'))->toHaveCount(2);
});

it('drops a single letter from a query, since nothing is indexed that short', function () {
    expect(BlindIndex::hashesForQuery('a'))->toBe([]);
    expect(BlindIndex::hashesForQuery('a perk'))->toHaveCount(1);
});

it('returns nothing for an empty value', function () {
    expect(BlindIndex::hashesFor(''))->toBe([]);
    expect(BlindIndex::hashesForQuery('   '))->toBe([]);
});

it('caps how many hashes one record can add', function () {
    $hashes = BlindIndex::hashesFor(implode(' ', array_fill(0, 200, 'monica geller')).' '.fake()->sentence(200));

    expect(count($hashes))->toBeLessThanOrEqual(150);
});
