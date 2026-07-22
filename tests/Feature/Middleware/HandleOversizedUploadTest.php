<?php

declare(strict_types=1);

use App\Http\Middleware\HandleOversizedUpload;
use Illuminate\Http\Request;

/*
 * The size in bytes of PHP's post_max_size, so the test can build a request that
 * is guaranteed to sit just above whatever the runner is configured with.
 */
function postMaxSizeInBytes(): int
{
    $value = trim((string) ini_get('post_max_size'));

    if ($value === '') {
        return 0;
    }

    $unit = strtolower($value[strlen($value) - 1]);
    $bytes = (int) $value;

    return match ($unit) {
        'g' => $bytes * 1024 * 1024 * 1024,
        'm' => $bytes * 1024 * 1024,
        'k' => $bytes * 1024,
        default => $bytes,
    };
}

function handleUpload(Request $request): mixed
{
    $request->setLaravelSession(app('session')->driver());
    app('url')->setRequest($request);

    return (new HandleOversizedUpload)->handle($request, fn () => response('passed through'));
}

it('sends the user back with an error when the body exceeds post_max_size', function () {
    $limit = postMaxSizeInBytes();

    if ($limit === 0) {
        $this->markTestSkipped('post_max_size is unlimited, so the overflow cannot be detected.');
    }

    $request = Request::create('http://localhost/items', 'POST');
    $request->server->set('CONTENT_LENGTH', $limit + 1024);

    $response = handleUpload($request);

    expect($response->getStatusCode())->toBe(302);
    expect($response->getSession()->get('error'))->toBe('The upload was too large. Please choose a smaller file and try again.');
});

it('lets a request within the limit through untouched', function () {
    $request = Request::create('http://localhost/items', 'POST', ['name' => 'Amazing Spider-Man #1']);
    $request->server->set('CONTENT_LENGTH', 500);

    $response = handleUpload($request);

    expect($response->getContent())->toBe('passed through');
});

it('does not mistake a large request that still carries a body for an overflow', function () {
    $limit = postMaxSizeInBytes();

    if ($limit === 0) {
        $this->markTestSkipped('post_max_size is unlimited, so the overflow cannot be detected.');
    }

    // Over the limit, but the input survived: not the empty body PHP leaves behind.
    $request = Request::create('http://localhost/items', 'POST', ['name' => 'Amazing Spider-Man #1']);
    $request->server->set('CONTENT_LENGTH', $limit + 1024);

    $response = handleUpload($request);

    expect($response->getContent())->toBe('passed through');
});
