<?php

declare(strict_types=1);
use App\Enums\DatePrecision;
use App\Helpers\ImpreciseDate;
use Carbon\Carbon;

it('renders the full day when the day is known', function () {
    expect(ImpreciseDate::format(Carbon::parse('1987-03-12'), DatePrecision::Exact))->toBe('March 12, 1987');
});

it('drops the day when only the month is known', function () {
    expect(ImpreciseDate::format(Carbon::parse('1987-03-12'), DatePrecision::Month))->toBe('March 1987');
});

it('keeps only the year when only the year is known', function () {
    expect(ImpreciseDate::format(Carbon::parse('1987-03-12'), DatePrecision::Year))->toBe('1987');
});

it('reads an approximate date as circa', function () {
    expect(ImpreciseDate::format(Carbon::parse('1987-03-12'), DatePrecision::Approximate))->toBe('circa 1987');
});

// The precision wins over whatever sits in the column. An event recorded as
// undated must not start reading as dated because an earlier edit left a date
// behind.
it('says the date is unknown whatever the column holds', function () {
    expect(ImpreciseDate::format(Carbon::parse('1987-03-12'), DatePrecision::Unknown))->toBe('Date unknown');
});

it('says the date is unknown when there is no date at all', function () {
    expect(ImpreciseDate::format(null, DatePrecision::Exact))->toBe('Date unknown');
    expect(ImpreciseDate::format(null, DatePrecision::Year))->toBe('Date unknown');
});

it('shortens each precision for a timeline column', function () {
    $date = Carbon::parse('1987-03-12');

    expect(ImpreciseDate::short($date, DatePrecision::Exact))->toBe('Mar 12, 1987');
    expect(ImpreciseDate::short($date, DatePrecision::Month))->toBe('Mar 1987');
    expect(ImpreciseDate::short($date, DatePrecision::Year))->toBe('1987');
    expect(ImpreciseDate::short($date, DatePrecision::Approximate))->toBe('c. 1987');
});

it('shortens an unknown date to a dash', function () {
    expect(ImpreciseDate::short(Carbon::parse('1987-03-12'), DatePrecision::Unknown))->toBe('—');
    expect(ImpreciseDate::short(null, DatePrecision::Exact))->toBe('—');
});

it('knows which precisions are worth recording a date against', function () {
    expect(DatePrecision::Exact->carriesDate())->toBeTrue();
    expect(DatePrecision::Month->carriesDate())->toBeTrue();
    expect(DatePrecision::Year->carriesDate())->toBeTrue();
    expect(DatePrecision::Approximate->carriesDate())->toBeTrue();
    expect(DatePrecision::Unknown->carriesDate())->toBeFalse();
});
