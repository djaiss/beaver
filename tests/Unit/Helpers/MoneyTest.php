<?php

declare(strict_types=1);
use App\Helpers\Money;

it('formats an amount with the symbol of a currency it knows', function () {
    expect(Money::format(cents: 42000, currency: 'USD'))->toBe('$420');
    expect(Money::format(cents: 150000, currency: 'EUR'))->toBe('€1,500');
});

it('spells out a currency it has no symbol for', function () {
    expect(Money::format(cents: 120000, currency: 'CHF'))->toBe('1,200 CHF');
});

it('returns the bare amount when there is no currency', function () {
    expect(Money::format(cents: 42000, currency: null))->toBe('420');
});

it('formats zero', function () {
    expect(Money::format(cents: 0, currency: 'USD'))->toBe('$0');
});
