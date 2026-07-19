<?php

declare(strict_types=1);
use App\Helpers\FileSize;

it('shows bytes whole', function () {
    expect(FileSize::format(0))->toBe('0 B');
    expect(FileSize::format(512))->toBe('512 B');
});

it('steps up to the next unit at 1024', function () {
    expect(FileSize::format(1024))->toBe('1 KB');
    expect(FileSize::format(1024 * 1024))->toBe('1 MB');
    expect(FileSize::format(1024 * 1024 * 1024))->toBe('1 GB');
});

it('keeps one decimal above bytes', function () {
    expect(FileSize::format(1536))->toBe('1.5 KB');
    expect(FileSize::format(3_264_500))->toBe('3.1 MB');
});

it('stops at terabytes', function () {
    expect(FileSize::format(1024 ** 5))->toBe('1024 TB');
});

it('treats a negative size as nothing', function () {
    expect(FileSize::format(-10))->toBe('0 B');
});
