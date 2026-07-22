<?php

declare(strict_types=1);

use App\Services\MarketingFeatures;

it('exposes three columns of feature areas', function () {
    $columns = (new MarketingFeatures())->columns();

    expect($columns)->toHaveCount(3);
    expect($columns[0]['items'])->toHaveCount(4);
});

it('flattens every feature area into a single list', function () {
    expect((new MarketingFeatures())->all())->toHaveCount(12);
});

it('finds a feature area by its slug', function () {
    $feature = (new MarketingFeatures())->find('copy-tracking');

    expect($feature)->not->toBeNull();
    expect($feature['title'])->toBe('Copy tracking');
});

it('returns null for an unknown slug', function () {
    expect((new MarketingFeatures())->find('nope'))->toBeNull();
});

it('marks custom catalogues as new', function () {
    expect((new MarketingFeatures())->find('custom-catalogues')['isNew'])->toBeTrue();
});
