<?php

declare(strict_types=1);
use App\Helpers\TextSanitizer;

test('plain text strips tags and trims', function () {
    expect(TextSanitizer::plainText('  <p>Hello</p> <b>World</b>  '))->toBe('Hello World');
});

test('plain text removes script tags', function () {
    expect(TextSanitizer::plainText('<script>alert("xss")</script>'))->toBe('');
});

test('plain text handles malformed html', function () {
    $result = TextSanitizer::plainText('< script >alert(1)</ script >');

    $this->assertStringNotContainsString('< script >', $result);
});

test('nullable plain text returns null for null', function () {
    expect(TextSanitizer::nullablePlainText(null))->toBeNull();
});

test('nullable plain text returns null for empty results', function () {
    expect(TextSanitizer::nullablePlainText('<p></p>'))->toBeNull();
    expect(TextSanitizer::nullablePlainText('   '))->toBeNull();
});

test('html strips dangerous tags but preserves safe ones', function () {
    $result = TextSanitizer::html('<p>Hello</p><script>alert(1)</script>');

    $this->assertStringContainsString('<p>Hello</p>', $result);
    $this->assertStringNotContainsString('<script>', $result);
});

test('nullable html returns null for empty or dangerous only', function () {
    expect(TextSanitizer::nullableHtml(null))->toBeNull();
    expect(TextSanitizer::nullableHtml('<script>alert(1)</script>'))->toBeNull();
    expect(TextSanitizer::nullableHtml('<p>   </p>'))->toBeNull();
});
